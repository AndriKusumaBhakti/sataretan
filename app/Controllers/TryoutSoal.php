<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TryoutModel;
use App\Models\TryoutSoalModel;

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
        $tryout = $this->tryoutModel->find($tryoutId);

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
        $tryout = $this->tryoutModel->find($tryoutId);

        if (!$tryout) {
            return redirect()->back()->with('errors', 'Try Out tidak ditemukan');
        }
        $data['tryout'] = $tryout;

        return view('tryout/soal/tambah', $data);
    }

    /* ================================
       SIMPAN SOAL
    ================================= */
    public function simpan($kategori, $tryoutId)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $tryout = $this->tryoutModel->find($tryoutId);
        $gambarSoal = null;

        if (!$tryout) {
            return redirect()->back()->with('errors', 'Try Out tidak ditemukan');
        }

        // Validasi jumlah soal maksimum
        $jumlahSoalSekarang = $this->tryoutSoalModel
            ->where('tryout_id', $tryoutId)
            ->countAllResults();

        if ($jumlahSoalSekarang >= $tryout['jumlah_soal']) {
            return redirect()->to(site_url('tryout/' . $kategori . '/' . $tryoutId . '/soal'))->with('errors', 'Jumlah soal sudah memenuhi batas');
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

        /* ================================
           UPLOAD GAMBAR
        ================================= */
        $fileSoal = $this->request->getFile('gambar_soal');

        if ($fileSoal && $fileSoal->isValid() && !$fileSoal->hasMoved()) {
            $gambarSoal = $fileSoal->getRandomName();
            $path = WRITEPATH . 'uploads/soal';
            $fileSoal->move($path, $gambarSoal);
        }

        /* ================= GAMBAR OPSI ================= */
        $gambarOpsi = [];

        foreach (['A', 'B', 'C', 'D', 'E'] as $opsi) {
            $file = $this->request->getFile('gambar_opsi_' . $opsi);
            $gambarOpsi[$opsi] = null;

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $namaFile = $file->getRandomName();
                $path = WRITEPATH . 'uploads/soal';
                $file->move($path, $namaFile);
                $gambarOpsi[$opsi] = $namaFile;
            }
        }

        /* ================================
           SIMPAN DATABASE
        ================================= */
        $this->tryoutSoalModel->insert([
            'tryout_id'      => $tryoutId,
            'pertanyaan'     => $this->request->getPost('pertanyaan'),
            'gambar_soal'    => $gambarSoal,
            'opsi_A'         => $this->request->getPost('opsi_A'),
            'opsi_B'         => $this->request->getPost('opsi_B'),
            'opsi_C'         => $this->request->getPost('opsi_C'),
            'opsi_D'         => $this->request->getPost('opsi_D'),
            'opsi_E'         => $this->request->getPost('opsi_E'),
            'gambar_opsi_A'  => $gambarOpsi['A'],
            'gambar_opsi_B'  => $gambarOpsi['B'],
            'gambar_opsi_C'  => $gambarOpsi['C'],
            'gambar_opsi_D'  => $gambarOpsi['D'],
            'gambar_opsi_E'  => $gambarOpsi['E'],
            'jawaban_benar'  => $this->request->getPost('jawaban_benar')
        ]);

        return redirect()->to(site_url('tryout/' . $kategori . '/start/' . $tryoutId))
            ->with('success', 'Soal berhasil ditambahkan');
    }

    public function edit($kategori, $tryoutId, $soalId)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $tryout = $this->tryoutModel->find($tryoutId);
        if (!$tryout) {
            return redirect()->back()->with('errors', 'Try Out tidak ditemukan');
        }
        $data['tryout'] = $tryout;

        $soal   = $this->tryoutSoalModel->find($soalId);

        if (!$soal || $soal['tryout_id'] != $tryoutId) {
            return redirect()->back()->with('errors', 'Soal tidak ditemukan');
        }
        $data['soal'] = $soal;

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
        }

        /* ================= GAMBAR OPSI ================= */
        $gambarOpsi = [];

        foreach (['A', 'B', 'C', 'D', 'E'] as $opsi) {
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
            }
        }

        /* ================= UPDATE DATABASE ================= */
        $this->tryoutSoalModel->update($soalId, [
            'pertanyaan'     => $this->request->getPost('pertanyaan'),
            'gambar_soal'    => $gambarSoal,
            'opsi_A'         => $this->request->getPost('opsi_A'),
            'opsi_B'         => $this->request->getPost('opsi_B'),
            'opsi_C'         => $this->request->getPost('opsi_C'),
            'opsi_D'         => $this->request->getPost('opsi_D'),
            'opsi_E'         => $this->request->getPost('opsi_E'),
            'gambar_opsi_A'  => $gambarOpsi['A'],
            'gambar_opsi_B'  => $gambarOpsi['B'],
            'gambar_opsi_C'  => $gambarOpsi['C'],
            'gambar_opsi_D'  => $gambarOpsi['D'],
            'gambar_opsi_E'  => $gambarOpsi['E'],
            'jawaban_benar'  => $this->request->getPost('jawaban_benar'),
        ]);

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
}
