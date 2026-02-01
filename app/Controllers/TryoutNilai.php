<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TryoutModel;
use App\Models\TryoutSoalModel;
use App\Models\TryoutJawabanModel;
use App\Models\TryoutAttemptModel;
use App\Models\UserPaketModel;
use App\Models\ParameterModel;

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TryoutNilai extends BaseController
{
    protected $tryoutModel;
    protected $tryoutSoalModel;
    protected $tryoutjawabanModel;
    protected $tryoutattemptModel;
    protected array $menuItems = [];
    protected $userPaketModel;
    protected $parameter;

    public function __construct()
    {
        helper('auth');
        $this->menuItems = user_menu();
        $this->tryoutModel = new TryoutModel();
        $this->tryoutSoalModel = new TryoutSoalModel();
        $this->tryoutjawabanModel = new tryoutjawabanModel();
        $this->tryoutattemptModel  = new TryoutAttemptModel();
        $this->userPaketModel = new UserPaketModel();
        $this->parameter = new ParameterModel();
    }

    private function baseData(): array
    {
        $this->checkDatabase();
        $data = default_parser_item([]); // Pastikan helper ini tersedia
        $data['menuItems'] = $this->menuItems;
        $data['base_url'] = base_url('/');
        $data['isGuruOrAdmin'] = isGuruOrAdmin();
        return $data;
    }

    public function nilai($kategori, $tryoutId)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $tryoutQuery = $this->tryoutModel
            ->where('id', $tryoutId)
            ->where('kategori', $kategori);

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $tryoutQuery->where('company_id', companyId());
        }

        $tryout = $tryoutQuery->first();

        if (!$tryout) {
            return redirect()->back()->with('errors', ['Tryout tidak ditemukan']);
        }
        
        $attempts = $this->tryoutattemptModel->getDaftarNilai($tryoutId);

        $hasOnline = false;
        $pilihanJson  = json_decode($tryout['ujian']);
        $parameter = $this->parameter->getValue($kategori);
        foreach ($pilihanJson as $prog => $keyPilihan) {
            foreach ($parameter as $item) {
                if (
                    $item['key'] === $keyPilihan &&
                    $item['mode'] === 'online'
                ) {
                    $hasOnline = true;
                    break 2;
                }
            }
        }

        $data['tryoutId'] = $tryoutId;
        $data['nilai'] = $attempts;
        $data['hasOnline'] = $hasOnline;

        return view('tryout/tryout/nilai', $data);
    }

    public function reset($kategori, $id)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $attempts = $this->tryoutattemptModel->find($id);

        if (!$attempts) {
            return redirect()->back()->with('errors', 'Data tidak ditemukan');
        }

        $tryout = $this->tryoutModel->where('id', $attempts['tryout_id'])->where('kategori', $kategori)->first();

        if (!$tryout) {
            return redirect()->back()->with('errors', ['Tryout tidak ditemukan']);
        }

        $this->db->transStart();

        try {
            // === Hapus jawaban peserta ===
            $this->tryoutjawabanModel
                ->where('tryout_id', $attempts['tryout_id'])
                ->where('user_id', $attempts['user_id'])
                ->delete();

            // === Reset data peserta ===
            // === Hapus jawaban peserta ===
            $this->tryoutattemptModel->delete($id);

            $this->db->transComplete();

            return redirect()->back()
                ->with('success', 'Nilai berhasil di-reset');
        } catch (\Throwable $e) {

            $this->db->transRollback();

            return redirect()->back()
                ->with('errors', 'Gagal reset nilai');
        }
    }

    public function detail($kategori, $id)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $attempts = $this->tryoutattemptModel->find($id);

        if (!$attempts) {
            return redirect()->back()->with('errors', 'Data tidak ditemukan');
        }

        $tryout = $this->tryoutModel->where('id', $attempts['tryout_id'])->where('kategori', $kategori)->first();

        if (!$tryout) {
            return redirect()->back()->with('errors', ['Tryout tidak ditemukan']);
        }

        $hasOnline = false;
        $pilihanJson  = json_decode($tryout['ujian']);
        $parameter = $this->parameter->getValue($kategori);
        foreach ($pilihanJson as $prog => $keyPilihan) {
            foreach ($parameter as $item) {
                if (
                    $item['key'] === $keyPilihan &&
                    $item['mode'] === 'online'
                ) {
                    $hasOnline = true;
                    break 2;
                }
            }
        }

        if ($hasOnline) {
            $soalList = $this->tryoutSoalModel->where('tryout_id', $tryout['id'])->findAll();

            $totalSoal = count($soalList);

            $benar = 0;
            $detail = [];
            foreach ($soalList as $soal) {
                $j = $this->tryoutjawabanModel
                    ->where('soal_id', $soal['id'])
                    ->where('user_id', user_id())
                    ->first();

                $jawaban_user = $j['jawaban'] ?? null;

                $isBenar = $jawaban_user === $soal['jawaban_benar'];

                if ($isBenar) {
                    $benar++;
                }

                $detail[] = [
                    'pertanyaan' => $soal['pertanyaan'],
                    'jawaban_user' => $jawaban_user,
                    'jawaban_benar' => $soal['jawaban_benar'],
                    'benar' => $isBenar,
                    'opsi_a' => $soal['opsi_A'],
                    'opsi_b' => $soal['opsi_B'],
                    'opsi_c' => $soal['opsi_C'],
                    'opsi_d' => $soal['opsi_D'],
                    'opsi_e' => $soal['opsi_E'],
                    'nilai_a' => $soal['nilai_A'],
                    'nilai_b' => $soal['nilai_B'],
                    'nilai_c' => $soal['nilai_C'],
                    'nilai_d' => $soal['nilai_D'],
                    'nilai_e' => $soal['nilai_E'],
                ];
            }

            $salah = $totalSoal - $benar;

            $data['total'] = $totalSoal;
            $data['benar'] = $benar;
            $data['salah'] = $salah;
            $data['nilai'] = $attempts['skor_akhir'];
            $data['detail'] = $detail;
        } else {
            $data['total'] = 0;
            $data['benar'] = 0;
            $data['salah'] = 0;
            $data['nilai'] = $attempts['skor_akhir'];
            $data['detail'] = [];
        }
        $data['hasOnline'] = $hasOnline;
        $data['tryout'] = $tryout;

        return view('tryout/tryout/detail-nilai', $data);
    }

    public function exportPdf($kategori, $tryoutId)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $tryoutQuery = $this->tryoutModel
            ->where('id', $tryoutId);

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $tryoutQuery->where('company_id', companyId());
        }

        $tryout = $tryoutQuery->first();

        if (!$tryout) {
            return redirect()->back()->with('errors', ['Tryout tidak ditemukan']);
        }

        $nilai = $this->tryoutattemptModel->getDaftarNilai($tryoutId);

        if (empty($nilai)) {
            return redirect()->back()->with('error', 'Data nilai kosong');
        }

        $html = view('tryout/tryout/nilai-pdf', [
            'nilai' => $nilai,
            'judul' => 'Daftar Nilai Try Out'
        ]);

        $dompdf = new Dompdf([
            'isRemoteEnabled' => true,
            'defaultFont' => 'Helvetica'
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader(
                'Content-Disposition',
                'attachment; filename="nilai_tryout_' . date('Ymd_His') . '.pdf"'
            )
            ->setBody($dompdf->output());
    }

    /* ===================================================== */

    public function exportExcel($kategori, $tryoutId)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $tryoutQuery = $this->tryoutModel
            ->where('id', $tryoutId);

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $tryoutQuery->where('company_id', companyId());
        }

        $tryout = $tryoutQuery->first();

        if (!$tryout) {
            return redirect()->back()->with('errors', ['Tryout tidak ditemukan']);
        }

        $nilai = $this->tryoutattemptModel->getDaftarNilai($tryoutId);

        if (empty($nilai)) {
            return redirect()->back()->with('error', 'Data nilai kosong');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ===== HEADER =====
        $headers = ['No', 'Nama', 'Mulai', 'Selesai', 'Nilai', 'Status'];
        $sheet->fromArray($headers, null, 'A1');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        // ===== DATA =====
        $row = 2;
        foreach ($nilai as $i => $n) {
            $sheet->fromArray([
                $i + 1,
                $n['nama'],
                date('d M Y H:i', strtotime($n['started_at'])),
                $n['finished_at']
                    ? date('d M Y H:i', strtotime($n['finished_at']))
                    : '-',
                $n['skor_akhir'],
                ucfirst($n['status'])
            ], null, 'A' . $row);
            $row++;
        }

        // ===== AUTO WIDTH =====
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'nilai_tryout_' . date('Ymd_His') . '.xlsx';

        return $this->response
            ->setHeader(
                'Content-Type',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            )
            ->setHeader(
                'Content-Disposition',
                'attachment; filename="' . $filename . '"'
            )
            ->setBody($this->writeExcel($writer));
    }

    public function exportExcelRekap($kategori)
    {
        // ===============================
        // 1. AMBIL SEMUA TRYOUT PER PROGRAM
        // ===============================
        $tryouts = $this->db->table('tryout')
            ->select('id, judul, program, kategori')
            ->where('status', 'aktif')
            ->orderBy('program')
            ->orderBy('id')
            ->get()
            ->getResultArray();

        // Group tryout by program
        $tryoutByProgram = [];
        foreach ($tryouts as $t) {
            $tryoutByProgram[$t['program']][] = $t;
        }

        // ===============================
        // 2. AMBIL USER + PROGRAM
        // ===============================
        $users = $this->db->table('users u')
            ->select('u.id, u.name, up.program')
            ->join('user_paket up', 'up.user_id = u.id', 'left')
            ->where('up.status', 'A')
            ->orderBy('u.name')
            ->get()
            ->getResultArray();

        // ===============================
        // 3. AMBIL NILAI TRYOUT
        // ===============================
        $attempts = $this->db->table('tryout_attempts')
            ->select('user_id, tryout_id, skor_akhir')
            ->where('status', 'finished')
            ->get()
            ->getResultArray();

        // Index nilai: user_id + tryout_id
        $nilaiMap = [];
        foreach ($attempts as $a) {
            $nilaiMap[$a['user_id']][$a['tryout_id']] = $a['skor_akhir'];
        }

        // ===============================
        // 4. BUAT EXCEL
        // ===============================
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;

        foreach ($tryoutByProgram as $program => $listTryout) {

            // ===== JUDUL PROGRAM =====
            $sheet->setCellValue("A$row", strtoupper($program));
            $sheet->mergeCells("A$row:Z$row");
            $sheet->getStyle("A$row")->getFont()->setBold(true)->setSize(14);
            $row += 2;

            // ===== HEADER =====
            $sheet->setCellValue("A$row", "NO");
            $sheet->setCellValue("B$row", "NAMA");

            $col = 'C';
            foreach ($listTryout as $t) {
                $sheet->setCellValue($col . $row, $t['judul']);
                $col++;
            }

            $sheet->setCellValue($col . $row, "RATA-RATA");

            $sheet->getStyle("A$row:$col$row")->getFont()->setBold(true);
            $row++;

            // ===== DATA USER =====
            $no = 1;
            foreach ($users as $u) {

                if (strtolower($u['program']) !== strtolower($program)) {
                    continue;
                }

                $sheet->setCellValue("A$row", $no++);
                $sheet->setCellValue("B$row", $u['name']);

                $col = 'C';
                $total = 0;
                $count = 0;

                foreach ($listTryout as $t) {
                    $nilai = $nilaiMap[$u['id']][$t['id']] ?? null;

                    if ($nilai !== null) {
                        $sheet->setCellValue($col . $row, round($nilai, 2));
                        $total += $nilai;
                        $count++;
                    } else {
                        $sheet->setCellValue($col . $row, "-");
                    }
                    $col++;
                }

                $sheet->setCellValue(
                    $col . $row,
                    $count ? round($total / $count, 2) : "-"
                );

                $row++;
            }

            $row += 2;
        }

        // ===============================
        // 5. STYLE GLOBAL
        // ===============================
        foreach (range('A', 'Z') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        // ===============================
        // 6. DOWNLOAD
        // ===============================
        $filename = 'rekap_nilai_tryout_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /* ===== helper stream ===== */
    private function writeExcel($writer)
    {
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }
}
