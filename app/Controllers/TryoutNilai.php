<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TryoutModel;
use App\Models\TryoutSoalModel;
use App\Models\TryoutJawabanModel;
use App\Models\TryoutAttemptModel;
use App\Models\UserPaketModel;

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

    public function __construct()
    {
        helper('auth');
        $this->menuItems = user_menu();
        $this->tryoutModel = new TryoutModel();
        $this->tryoutSoalModel = new TryoutSoalModel();
        $this->tryoutjawabanModel = new tryoutjawabanModel();
        $this->tryoutattemptModel  = new TryoutAttemptModel();
        $this->userPaketModel = new UserPaketModel();
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
        $tryout = $this->tryoutModel->where('id', $tryoutId)->where('kategori', $kategori)->first();

        if (!$tryout) {
            return redirect()->back()->with('errors', ['Tryout tidak ditemukan']);
        }

        $attempts = $this->tryoutattemptModel->getDaftarNilai($tryoutId);
        $totalSoal = $this->tryoutSoalModel
            ->where('tryout_id', $tryoutId)
            ->countAllResults();

        foreach ($attempts as &$row) {
            // hitung skor akhir (AMAN)
            $skor_akhir = $totalSoal > 0
                ? round(($row['skor_akhir'] / $totalSoal) * 100, 2)
                : 0;

            // inject ke array buat view
            $row['skor_akhir'] = $skor_akhir;
        }

        $data['tryoutId'] = $tryoutId;
        $data['nilai'] = $attempts;

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

        $jawaban = $this->tryoutjawabanModel
            ->where('user_id', $attempts['user_id'])
            ->where('tryout_id', $attempts['tryout_id'])
            ->findAll();

        $totalSoal = $this->tryoutSoalModel
            ->where('tryout_id', $attempts['tryout_id'])
            ->countAllResults();

        $benar = 0;
        $detail = [];

        foreach ($jawaban as $j) {
            $soal = $this->tryoutSoalModel->find($j['soal_id']);

            if (!$soal) {
                continue;
            }

            $isBenar = $soal['jawaban_benar'] === $j['jawaban'];

            if ($isBenar) {
                $benar++;
            }

            $detail[] = [
                'pertanyaan' => $soal['pertanyaan'],
                'jawaban_user' => $j['jawaban'],
                'jawaban_benar' => $soal['jawaban_benar'],
                'benar' => $isBenar
            ];
        }

        $salah = $totalSoal - $benar;
        $nilai = round(($benar / $totalSoal) * 100, 2);

        $data['tryout'] = $tryout;
        $data['total'] = $totalSoal;
        $data['benar'] = $benar;
        $data['salah'] = $salah;
        $data['nilai'] = $nilai;
        $data['detail'] = $detail;

        return view('tryout/tryout/detail-nilai', $data);
    }

    public function exportPdf($kategori, $tryoutId)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $tryout = $this->tryoutModel->find($tryoutId);

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
        $tryout = $this->tryoutModel->find($tryoutId);

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

    /* ===== helper stream ===== */
    private function writeExcel($writer)
    {
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }
}
