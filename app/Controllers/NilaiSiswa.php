<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TryoutModel;
use App\Models\TryoutSoalModel;
use App\Models\TryoutJawabanModel;
use App\Models\TryoutAttemptModel;
use App\Models\UserPaketModel;
use App\Models\ParameterModel;
use App\Models\JasmaniModel;
use \Config\Database;

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class NilaiSiswa extends BaseController
{
    protected $tryoutModel;
    protected $tryoutSoalModel;
    protected $tryoutjawabanModel;
    protected $tryoutattemptModel;
    protected array $menuItems = [];
    protected $userPaketModel;
    protected $parameter;
    protected $jasmani;

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
        $this->jasmani = new JasmaniModel();
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

    public function export()
    {
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');

        if (!$bulan || !$tahun) {
            return redirect()->back()->with('warning', 'Bulan dan Tahun wajib dipilih');
        }

        $db = \Config\Database::connect();

        /* ================= MASTER ================= */
        $akademikMaster = $this->parameter->getValue('akademik');
        $psikologMaster = $this->parameter->getValue('psikolog');
        $mapping        = $this->parameter->getValue('filter_program');

        $akdPersen = [];
        foreach ($akademikMaster as $m) {
            $akdPersen[$m['key']] = (float)$m['persen'];
        }

        $psiPersen = [];
        foreach ($psikologMaster as $m) {
            $psiPersen[$m['key']] = (float)$m['persen'];
        }

        $finalBobot = ['akademik' => 30, 'psikolog' => 35, 'jasmani' => 35];

        /* ================= SISWA ================= */
        $siswa = $db->table('users u')
            ->select('u.id, u.name, up.program')
            ->join('user_paket up', 'up.user_id=u.id')
            ->where('up.status', 'A')
            ->orderBy('u.name', 'ASC')
            ->get()->getResultArray();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach (['polri', 'kedinasan', 'tni'] as $program) {

            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle(strtoupper($program));

            /* ================= TITLE ================= */
            $sheet->mergeCells('A1:Z1');
            $sheet->setCellValue('A1', 'REKAP NILAI TRYOUT - ' . strtoupper($program));
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

            /* ================= STYLE ================= */
            $styleHeader = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'borders' => ['allBorders' => ['borderStyle' => 'thin']]
            ];

            $styleAkd   = ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E8F5E9']]];
            $stylePsi   = ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFFDE7']]];
            $styleFinal = ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E3F2FD']]];

            /* ================= HEADER ================= */
            $sheet->setCellValue('A2', 'NO')
                ->setCellValue('B2', 'NAMA');
            $sheet->mergeCells('A2:A3')
                ->mergeCells('B2:B3');

            $col = 'C';

            /* ================= AKADEMIK ================= */
            $startAkd = $col;
            foreach ($akademikMaster as $m) {
                if (!in_array($m['key'], $mapping['akademik'][$program])) continue;

                $sheet->setCellValue($col . '3', $m['value']);
                $col++;
                $sheet->setCellValue($col . '3', $m['value'] . ' (' . $akdPersen[$m['key']] . '%)');
                $col++;
            }
            $sheet->setCellValue($col . '3', 'NILAI AKADEMIK');
            $nilaiAkdCol = $col;

            $sheet->mergeCells($startAkd . '2:' . $col . '2')
                ->setCellValue($startAkd . '2', 'AKADEMIK');
            $col++;

            /* ================= PSIKOLOG ================= */
            $startPsi = $col;
            foreach ($psikologMaster as $m) {
                if (!in_array($m['key'], $mapping['psikolog'][$program])) continue;

                $sheet->setCellValue($col . '3', $m['value']);
                $col++;
                $sheet->setCellValue($col . '3', $m['value'] . ' (' . $psiPersen[$m['key']] . '%)');
                $col++;
            }
            $sheet->setCellValue($col . '3', 'NILAI PSIKOLOG');
            $nilaiPsiCol = $col;

            $sheet->mergeCells($startPsi . '2:' . $col . '2')
                ->setCellValue($startPsi . '2', 'PSIKOLOG');
            $col++;

            /* ================= JASMANI ================= */
            $showJasmani = ($program !== 'kedinasan'); // Kedinasan tidak pakai Jasmani

            if ($showJasmani) {
                $sheet->setCellValue($col . '2', 'JASMANI');
                $sheet->mergeCells($col . '2:' . $col . '3');
                $sheet->setCellValue($col . '3', 'NILAI JASMANI');
                $nilaiJasCol = $col++;
            }

            /* ================= NILAI AKHIR ================= */
            $startFinal = $col;

            $sheet->setCellValue($col . '3', 'AKADEMIK');
            $akdFinalCol = $col++;

            $sheet->setCellValue($col . '3', 'PSIKOLOG');
            $psiFinalCol = $col++;

            if ($showJasmani) {
                $sheet->setCellValue($col . '3', 'JASMANI');
                $jasFinalCol = $col++;
            }

            $sheet->setCellValue($col . '3', 'TOTAL');
            $nilaiAkhirCol = $col;

            $sheet->mergeCells($startFinal . '2:' . $nilaiAkhirCol . '2')
                ->setCellValue($startFinal . '2', 'NILAI AKHIR');

            /* ================= DATA ================= */
            $row = 4;
            $no  = 1;

            foreach ($siswa as $s) {
                if ($s['program'] !== $program) continue;

                $nilaiAkd = array_fill_keys($mapping['akademik'][$program], 0);
                $nilaiPsi = array_fill_keys($mapping['psikolog'][$program], 0);

                /* ================== Ambil Nilai Jasmani ================== */
                $nilaiJas = 0;
                if ($showJasmani) {
                    $jasmani = $this->jasmani
                        ->select('jasmani.*, users.name, users.email')
                        ->join('users', 'users.id = jasmani.user_id', 'left')
                        ->when(!isSuperAdmin(), function ($query) {
                            $query->where('users.company_id', companyId());
                        })
                        ->where('jasmani.user_id', $s['id'])
                        ->first();

                    if ($jasmani) {
                        if ($jasmani['kategori'] === 'tni') {
                            $nilaiJas = $this->hitungTotalTni($jasmani);
                        } else {
                            $nilaiJas = $this->hitungTotalPolri($jasmani);
                        }
                    }
                }

                /* ================== Ambil Nilai Tryout ================== */
                $attempts = $db->table('tryout_attempts ta')
                    ->select('t.kategori, t.ujian, ta.skor_akhir')
                    ->join('tryout t', 't.id=ta.tryout_id')
                    ->where('ta.user_id', $s['id'])
                    ->where('MONTH(ta.finished_at)', $bulan)
                    ->where('YEAR(ta.finished_at)', $tahun)
                    ->get()->getResultArray();

                foreach ($attempts as $a) {
                    $uj = json_decode($a['ujian'], true);
                    if (!isset($uj[$program])) continue;

                    $k = strtolower($uj[$program]);
                    if ($a['kategori'] === 'akademik' && isset($nilaiAkd[$k])) {
                        $nilaiAkd[$k] = $a['skor_akhir'];
                    }
                    if ($a['kategori'] === 'psikolog' && isset($nilaiPsi[$k])) {
                        $nilaiPsi[$k] = $a['skor_akhir'];
                    }
                }

                /* ================== Isi ke Sheet ================== */
                $sheet->setCellValue("A$row", $no++)
                    ->setCellValue("B$row", $s['name']);

                /* Akademik */
                $c = $startAkd;
                $akdCells = [];
                foreach ($nilaiAkd as $k => $v) {
                    $sheet->setCellValue($c . $row, $v);
                    $c++;
                    $sheet->setCellValue($c . $row, round($v * $akdPersen[$k] / 100, 2));
                    $akdCells[] = $c . $row;
                    $c++;
                }
                $sheet->setCellValue($nilaiAkdCol . $row, '=SUM(' . implode(',', $akdCells) . ')');

                /* Psikolog */
                $c = $startPsi;
                $psiCells = [];
                foreach ($nilaiPsi as $k => $v) {
                    $sheet->setCellValue($c . $row, $v);
                    $c++;
                    $sheet->setCellValue($c . $row, round($v * $psiPersen[$k] / 100, 2));
                    $psiCells[] = $c . $row;
                    $c++;
                }
                $sheet->setCellValue($nilaiPsiCol . $row, '=SUM(' . implode(',', $psiCells) . ')');

                /* Final */
                if ($showJasmani) {
                    $sheet->setCellValue($nilaiJasCol . $row, $nilaiJas);
                }

                $sheet->setCellValue($akdFinalCol . $row, "={$nilaiAkdCol}{$row}*{$finalBobot['akademik']}/100");
                $sheet->setCellValue($psiFinalCol . $row, "={$nilaiPsiCol}{$row}*{$finalBobot['psikolog']}/100");

                if ($showJasmani) {
                    $sheet->setCellValue($jasFinalCol . $row, "={$nilaiJasCol}{$row}*{$finalBobot['jasmani']}/100");
                    $sheet->setCellValue($nilaiAkhirCol . $row, "=SUM({$akdFinalCol}{$row},{$psiFinalCol}{$row},{$jasFinalCol}{$row})");
                } else {
                    $sheet->setCellValue($nilaiAkhirCol . $row, "=SUM({$akdFinalCol}{$row},{$psiFinalCol}{$row})");
                }

                $row++;
            }

            /* ================= STYLE FINAL ================= */
            $lastCol = $sheet->getHighestColumn();
            $lastRow = $sheet->getHighestRow();

            $sheet->getStyle("A2:{$lastCol}3")->applyFromArray($styleHeader);
            $sheet->getStyle("{$startAkd}2:{$nilaiAkdCol}{$lastRow}")->applyFromArray($styleAkd);
            $sheet->getStyle("{$startPsi}2:{$nilaiPsiCol}{$lastRow}")->applyFromArray($stylePsi);
            $sheet->getStyle("{$startFinal}2:{$nilaiAkhirCol}{$lastRow}")->applyFromArray($styleFinal);

            $sheet->getStyle("{$nilaiAkhirCol}4:{$nilaiAkhirCol}{$lastRow}")
                ->getFont()->setBold(true);

            $sheet->freezePane('C4');

            foreach (range('A', $lastCol) as $c) {
                $sheet->getColumnDimension($c)->setAutoSize(true);
            }
        }

        $filename = "Rekap_Nilai_{$bulan}_{$tahun}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save('php://output');
        exit;
    }


    private function hitungTotalTni(array $data)
    {
        $nilai = array_filter([
            $data['nilai_lari_12'],
            $data['nilai_garjas_b'],
        ], 'is_numeric');

        return count($nilai)
            ? round(array_sum($nilai) / count($nilai), 2)
            : null;
    }

    private function hitungTotalPolri(array $data)
    {
        $nilai =  ($data['jenis_kelamin'] === 'pria') ? array_filter([
            $data['nilai_lari_12'],
            $data['nilai_pull_up'],
            $data['nilai_sit_up'],
            $data['nilai_push_up'],
            $data['nilai_shuttle_run'],
            $data['nilai_renang'],
        ], 'is_numeric') :  array_filter([
            $data['nilai_lari_12'],
            $data['nilai_chinning'],
            $data['nilai_sit_up'],
            $data['nilai_push_up'],
            $data['nilai_shuttle_run'],
            $data['nilai_renang'],
        ], 'is_numeric');

        return count($nilai)
            ? round(array_sum($nilai) / count($nilai), 2)
            : null;
    }
}
