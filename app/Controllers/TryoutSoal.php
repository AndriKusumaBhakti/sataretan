<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TryoutModel;
use App\Models\TryoutSoalModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TryoutSoal extends BaseController
{
    protected $tryoutModel;
    protected $tryoutSoalModel;
    protected array $menuItems = [];

    public function __construct()
    {
        $this->menuItems = user_menu();
        $this->tryoutModel = new TryoutModel();
        $this->tryoutSoalModel = new TryoutSoalModel();
    }

    private function baseData(): array
    {
        $this->checkDatabase();
        helper('text'); // 🔥 INI WAJIB
        $data = default_parser_item([]); // Pastikan helper ini tersedia
        $data['menuItems'] = $this->menuItems;
        $data['base_url'] = base_url('/');
        $data['isGuruOrAdmin'] = isGuruOrAdmin();
        return $data;
    }

    public function index($kategori, $tryoutId)
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
            return redirect()->back()->with('errors', 'Try Out tidak ditemukan');
        }
        $data['tryout'] = $tryout;

        $soal = $this->tryoutSoalModel
            ->where('tryout_id', $tryoutId)
            ->orderBy('id', 'ASC')
            ->findAll();
        $data['soalList'] = $soal;

        return view('tryout/soal/index', $data);
    }

    public function tambah($kategori, $tryoutId)
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
            return redirect()->back()->with('errors', 'Try Out tidak ditemukan');
        }
        $data['tryout'] = $tryout;

        $dbPilihan = $tryout['ujian'] ?? [];
        if (is_string($dbPilihan)) {
            $decoded = json_decode($dbPilihan, true);
            $dbPilihan = is_array($decoded) ? $decoded : [];
        }
        $formTkp = false;
        foreach ($dbPilihan  as $prog => $keys):
            if ($keys == 'kprd' || $keys == 'tkp') {
                $formTkp = true;
                continue;
            }
        endforeach;
        $data['formTkp'] = $formTkp;
        return view('tryout/soal/tambah', $data);
    }

    /* ================================
       SIMPAN SOAL
    ================================= */
    public function simpan($kategori, $tryoutId)
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
            return redirect()->back()->with('errors', 'Try Out tidak ditemukan');
        }

        // Validasi jumlah soal maksimum
        $jumlahSoalSekarang = $this->tryoutSoalModel
            ->where('tryout_id', $tryoutId)
            ->countAllResults();

        if ($jumlahSoalSekarang >= $tryout['jumlah_soal']) {
            return redirect()->to(site_url('tryout/' . $kategori . '/start/' . $tryoutId))
                ->with('errors', 'Jumlah soal sudah memenuhi batas');
        }

        /* ================================
           VALIDASI INPUT
        ================================= */
        $rules = [
            'pertanyaan'     => 'required',
            'jawaban_benar'  => 'required|in_list[A,B,C,D,E]',
            'gambar_soal'    => 'permit_empty|is_image[gambar_soal]|max_size[gambar_soal,2048]'
        ];

        foreach (['A', 'B', 'C', 'D', 'E'] as $opsi) {
            $rules["opsi_$opsi"] = 'required';
            $rules["gambar_opsi_$opsi"] =
                "permit_empty|is_image[gambar_opsi_$opsi]|max_size[gambar_opsi_$opsi,2048]";
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $insertdata = [
            'tryout_id' => $tryoutId,
            'pertanyaan' => $this->request->getPost('pertanyaan'),
            'jawaban_benar' => $this->request->getPost('jawaban_benar'),
        ];

        /* ================================
           UPLOAD GAMBAR
        ================================= */
        $fileSoal = $this->request->getFile('gambar_soal');
        $path = WRITEPATH . 'uploads/soal';

        if ($fileSoal && $fileSoal->isValid() && !$fileSoal->hasMoved()) {
            // hapus gambar lama
            $gambarSoal = $fileSoal->getRandomName();
            if ($gambarSoal && file_exists($path . $gambarSoal)) {
                unlink($path . $gambarSoal);
            }
            $fileSoal->move($path, $gambarSoal);
            $insertdata['gambar_soal'] = $gambarSoal;
        }

        foreach (['A', 'B', 'C', 'D', 'E'] as $opsi) {
            $insertdata['opsi_' . $opsi] = $this->request->getPost('opsi_' . $opsi);
            $insertdata['nilai_' . $opsi] = (int) $this->request->getPost('nilai_' . $opsi);

            $file = $this->request->getFile('gambar_opsi_' . $opsi);
            $gambarOpsi[$opsi] = null;

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $namaFile = $file->getRandomName();
                $file->move($path, $namaFile);
                $gambarOpsi[$opsi] = $namaFile;
                $insertdata['gambar_opsi_' . $opsi] = $namaFile;
            }
        }
        /* ================================
           SIMPAN DATABASE
        ================================= */
        $this->tryoutSoalModel->insert($insertdata);
        return redirect()->to(site_url('tryout/' . $kategori . '/' . $tryoutId . '/soal'))
                    ->with('success', 'Soal berhasil ditambahkan');
    }

    public function edit($kategori, $tryoutId, $soalId)
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
            return redirect()->back()->with('errors', 'Try Out tidak ditemukan');
        }
        $data['tryout'] = $tryout;

        $soal   = $this->tryoutSoalModel->find($soalId);

        if (!$soal || $soal['tryout_id'] != $tryoutId) {
            return redirect()->back()->with('errors', 'Soal tidak ditemukan');
        }
        $data['soal'] = $soal;
        $dbPilihan = $tryout['ujian'] ?? [];
        if (is_string($dbPilihan)) {
            $decoded = json_decode($dbPilihan, true);
            $dbPilihan = is_array($decoded) ? $decoded : [];
        }
        $formTkp = false;
        foreach ($dbPilihan  as $prog => $keys):
            if ($keys == 'kprd' || $keys == 'tkp') {
                $formTkp = true;
                continue;
            }
        endforeach;
        $data['formTkp'] = $formTkp;

        return view('tryout/soal/edit', $data);
    }

    public function update($kategori, $tryoutId, $soalId)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $soal = $this->tryoutSoalModel->find($soalId);

        if (!$soal) {
            return redirect()->back()->with('errors', 'Soal tidak ditemukan');
        }

        $updatedata = [
            'pertanyaan' => $this->request->getPost('pertanyaan'),
            'jawaban_benar' => $this->request->getPost('jawaban_benar'),
        ];

        /* ================= GAMBAR SOAL ================= */
        $fileSoal = $this->request->getFile('gambar_soal');
        $gambarSoal = $soal['gambar_soal']; // default lama

        $path = WRITEPATH . 'uploads/soal';
        if ($fileSoal && $fileSoal->isValid() && !$fileSoal->hasMoved()) {
            // hapus gambar lama
            if ($gambarSoal && file_exists($path . $gambarSoal)) {
                unlink($path . $gambarSoal);
            }

            $gambarSoal = $fileSoal->getRandomName();
            $fileSoal->move($path, $gambarSoal);
            $updatedata['gambar_soal'] = $gambarSoal;
        }

        /* ================= GAMBAR OPSI ================= */
        $gambarOpsi = [];

        foreach (['A', 'B', 'C', 'D', 'E'] as $opsi) {
            $updatedata['opsi_' . $opsi] = $this->request->getPost('opsi_' . $opsi);
            $updatedata['nilai_' . $opsi] = (int) $this->request->getPost('nilai_' . $opsi);

            $file = $this->request->getFile('gambar_opsi_' . $opsi);
            $gambarOpsi[$opsi] = $soal['gambar_opsi_' . $opsi]; // default lama

            if ($file && $file->isValid() && !$file->hasMoved()) {
                // hapus gambar lama
                if ($gambarOpsi[$opsi] && file_exists($path . $gambarOpsi[$opsi])) {
                    unlink($path . $gambarOpsi[$opsi]);
                }

                $namaFile = $file->getRandomName();
                $file->move($path, $namaFile);
                $gambarOpsi[$opsi] = $namaFile;
                $updatedata['gambar_opsi_' . $opsi] = $namaFile;
            }
        }

        /* ================= UPDATE DATABASE ================= */
        $this->tryoutSoalModel->update($soalId, $updatedata);

        return redirect()->to(site_url('tryout/' . $kategori . '/' . $tryoutId . '/soal'))
            ->with('success', 'Soal berhasil diperbarui');
    }

    public function hapus($kategori, $tryoutId, $soalId)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $soal = $this->tryoutSoalModel->find($soalId);
        if (!$soal) {
            return redirect()->back()->with('errors', 'Soal tidak ditemukan');
        }
        $path = WRITEPATH . 'uploads/soal';

        if ($soal) {

            if ($soal['gambar_soal'] && file_exists($path . $soal['gambar_soal'])) {
                unlink($path . $soal['gambar_soal']);
            }

            foreach (['A', 'B', 'C', 'D', 'E'] as $opsi) {
                if ($soal['gambar_opsi_' . $opsi] && file_exists($path . $soal['gambar_opsi_' . $opsi])) {
                    unlink($path . $soal['gambar_opsi_' . $opsi]);
                }
            }

            $this->tryoutSoalModel->delete($soalId);
        }

        return redirect()->back()->with('success', 'Soal berhasil dihapus');
    }

    public function uploadExcel($kategori, $tryoutId)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;

        $zipFile = $this->request->getFile('file_zip');

        if (!$zipFile || !$zipFile->isValid() || $zipFile->getClientExtension() !== 'zip') {
            $this->deleteDir($extractPath ?? null);
            return redirect()->back()->with('errors', ['File harus ZIP']);
        }

        // ================= EXTRACT ZIP =================
        $extractPath = WRITEPATH . 'uploads/file_soal/zip_' . time();
        mkdir($extractPath, 0777, true);

        $zip = new \ZipArchive();
        if ($zip->open($zipFile->getTempName()) !== true) {
            $this->deleteDir($extractPath);
            return redirect()->back()->with('errors', ['Gagal membuka ZIP']);
        }
        $zip->extractTo($extractPath);
        $zip->close();

        // ================= CARI EXCEL =================
        $excelFiles = glob($extractPath . '/*.xls*');
        if (empty($excelFiles)) {
            $this->deleteDir($extractPath);
            return redirect()->back()->with('errors', ['Excel tidak ditemukan di ZIP']);
        }

        $tryoutQuery = $this->tryoutModel->where('id', $tryoutId);
        if (!isSuperAdmin()) {
            $tryoutQuery->where('company_id', companyId());
        }

        $tryout = $tryoutQuery->first();
        if (!$tryout) {
            $this->deleteDir($extractPath);
            return redirect()->back()->with('errors', ['Try Out tidak ditemukan']);
        }

        // ================= VALIDASI JUMLAH SOAL =================
        $jumlahSoalSekarang = $this->tryoutSoalModel
            ->where('tryout_id', $tryoutId)
            ->countAllResults();

        if ($jumlahSoalSekarang >= $tryout['jumlah_soal']) {
            $this->deleteDir($extractPath);
            return redirect()->back()->with('errors', ['Jumlah soal sudah memenuhi batas']);
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFiles[0]);
        $rows = $spreadsheet->getActiveSheet()->toArray();
        unset($rows[0]); // hapus header

        $jumlahRowExcel = 0;
        foreach ($rows as $row) {
            if (!empty($row[0])) {
                $jumlahRowExcel++;
            }
        }

        $sisaSlot = $tryout['jumlah_soal'] - $jumlahSoalSekarang;
        if ($jumlahRowExcel > $sisaSlot) {
            $this->deleteDir($extractPath);
            return redirect()->back()->with(
                'errors',
                ["Jumlah soal di Excel ($jumlahRowExcel) melebihi sisa slot ($sisaSlot)"]
            );
        }

        // ================= VALIDASI GAMBAR =================
        $imgSource = $extractPath . '/gambar/';
        $missingImages = [];
        $rowNumber = 2; // excel mulai baris 2

        foreach ($rows as $row) {
            if (empty($row[0])) {
                $rowNumber++;
                continue;
            }

            $imageFields = [
                $row[7]  ?? null,
                $row[8]  ?? null,
                $row[9]  ?? null,
                $row[10] ?? null,
                $row[11] ?? null,
                $row[12] ?? null,
            ];

            foreach ($imageFields as $img) {
                if ($img && (!is_dir($imgSource) || !file_exists($imgSource . $img))) {
                    $missingImages[] = "Baris $rowNumber: gambar '$img' tidak ditemukan";
                }
            }

            $rowNumber++;
        }

        if (!empty($missingImages)) {
            $this->deleteDir($extractPath);
            return redirect()->back()->with('errors', $missingImages);
        }

        // ================= PINDAHKAN GAMBAR =================
        $targetPath = WRITEPATH . 'uploads/soal/';
        if (is_dir($imgSource)) {
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0777, true);
            }

            foreach (glob($imgSource . '*') as $img) {
                rename($img, $targetPath . basename($img));
            }
        }

        // ================= INSERT DB =================
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            foreach ($rows as $row) {

                if (empty($row[0])) {
                    continue;
                }

                $jawaban = strtoupper(trim($row[6]));
                if (!in_array($jawaban, ['A', 'B', 'C', 'D', 'E'])) {
                    continue;
                }

                $this->tryoutSoalModel->insert([
                    'tryout_id'     => $tryoutId,
                    'kategori'      => $kategori,
                    'pertanyaan'    => $row[0],
                    'opsi_A'        => $row[1],
                    'opsi_B'        => $row[2],
                    'opsi_C'        => $row[3],
                    'opsi_D'        => $row[4],
                    'opsi_E'        => $row[5],
                    'jawaban_benar' => $jawaban,
                    'gambar_soal'   => $row[7]  ?? null,
                    'gambar_opsi_A' => $row[8]  ?? null,
                    'gambar_opsi_B' => $row[9]  ?? null,
                    'gambar_opsi_C' => $row[10] ?? null,
                    'gambar_opsi_D' => $row[11] ?? null,
                    'gambar_opsi_E' => $row[12] ?? null,
                    'nilai_A' => $row[13]  ?? 0,
                    'nilai_B' => $row[14]  ?? 0,
                    'nilai_C' => $row[15] ?? 0,
                    'nilai_D' => $row[16] ?? 0,
                    'nilai_E' => $row[17] ?? 0,
                ]);
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            $this->deleteDir($extractPath);
            return redirect()->back()->with('errors', ['Gagal import soal']);
        }

        $this->deleteDir($extractPath);
        return redirect()
            ->to(site_url("tryout/$kategori/start/$tryoutId"))
            ->with('success', 'Soal berhasil ditambahkan');
    }

    private function deleteDir($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $path = $dir . '/' . $file;
                is_dir($path) ? $this->deleteDir($path) : unlink($path);
            }
        }
        rmdir($dir);
    }
}
