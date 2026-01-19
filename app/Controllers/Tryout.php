<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TryoutModel;
use App\Models\TryoutSoalModel;
use App\Models\TryoutJawabanModel;
use App\Models\TryoutAttemptModel;
use App\Models\UserPaketModel;

class Tryout extends BaseController
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

    // halaman list try out
    public function index($kategori)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        if ($kategori == "jasmani") {
            return redirect()->to(site_url('tryout/' . $kategori . '/view'));
        }
        $data['tryout'] = $this->tryoutModel->getTryoutStatistik($kategori, isGuruOrAdmin());

        return view('tryout/index', $data);
    }

    // halaman mulai try out
    public function start($kategori, $id)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;

        $tryout = $this->tryoutModel->find($id);

        if (!$tryout) {
            return redirect()->back()->with('errors', ['Tryout tidak ditemukan']);
        }
        $data['tryout'] = $tryout;
        $jumlahSoalTersedia = $this->tryoutSoalModel->countByTryout($id);
        $data['jumlahSoalTersedia'] = $jumlahSoalTersedia;
        $data['soalSiap'] = $jumlahSoalTersedia >= $tryout['jumlah_soal'];

        return view('tryout/start', $data);
    }

    public function tambah($kategori)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        return view('tryout/tryout/tambah', $data);
    }

    public function simpan($kategori)
    {
        $this->tryoutModel->insert([
            'kategori'     => $kategori,
            'judul'        => $this->request->getPost('judul'),
            'jumlah_soal'  => $this->request->getPost('jumlah_soal'),
            'durasi'       => $this->request->getPost('durasi'),
            'status'       => 'draft',
        ]);

        return redirect()->to(site_url('tryout/' . $kategori))->with('success', 'Try Out berhasil ditambahkan');
    }

    public function edit($kategori, $id)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $tryout = $this->tryoutModel->find($id);

        if (!$tryout) {
            return redirect()->back()->with('errors', 'Try Out tidak ditemukan');
        }
        $data['tryout'] = $tryout;
        return view('tryout/tryout/edit', $data);
    }

    public function update($kategori, $id)
    {
        $this->tryoutModel->update($id, [
            'judul'       => $this->request->getPost('judul'),
            'jumlah_soal' => $this->request->getPost('jumlah_soal'),
            'durasi'      => $this->request->getPost('durasi')
        ]);

        return redirect()->to(site_url('tryout/' . $kategori));
    }

    // ================= DELETE =================
    public function delete($kategori, $id)
    {
        $this->tryoutSoalModel
            ->where('tryout_id', $id)
            ->delete();
        $this->tryoutModel->delete($id);
        return redirect()->to(site_url('tryout/' . $kategori));
    }

    public function pengerjaan($kategori, $tryoutId, $nomor = 1)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;

        // Ambil tryout
        $tryout = $this->tryoutModel->find($tryoutId);
        if (!$tryout) {
            return redirect()->back()->with('errors', ['Tryout tidak ditemukan']);
        }

        $tryout_attempt = $this->tryoutattemptModel
            ->where('user_id', user_id())
            ->where('tryout_id', $tryoutId)
            ->first();
        if (!$tryout_attempt) {
            $this->tryoutattemptModel->insert([
                'user_id'    => user_id(),
                'tryout_id'  => $tryoutId,
                'started_at' => date('Y-m-d H:i:s'),
                'finished_at'   => date('Y-m-d H:i:s', time() + ($tryout['durasi'] * 60)),
                'skor_akhir'   => 0,
                'status'     => 'ongoing'
            ]);
            $tryout_attempt = $this->tryoutattemptModel
                ->where('user_id', user_id())
                ->where('tryout_id', $tryoutId)
                ->first();
        }
        $tryout_session_finished = $tryout_attempt['finished_at'];

        if ($tryout_attempt['status'] === 'finished') {
            return redirect()->to(site_url('tryout/' . $kategori . '/hasil/' . $tryoutId));
        }

        $data['tryout'] = $tryout;
        $endTime = strtotime($tryout_session_finished);
        $now     = time();
        $sisa    = max(0, $endTime - $now);

        $data['sisa_waktu'] = $sisa;

        // Semua soal
        $allSoal = $this->tryoutSoalModel
            ->where('tryout_id', $tryoutId)
            ->orderBy('id', 'ASC')
            ->findAll();

        $totalSoal = count($allSoal);
        if ($totalSoal == 0) {
            return redirect()->back()->with('errors', ['Tryout tidak ditemukan']);
        }

        // Validasi nomor
        if ($nomor < 1 || $nomor > $totalSoal) {
            return redirect()->to(
                site_url("tryout/$kategori/pengerjaan/$tryoutId/1")
            );
        }

        // Soal aktif
        $soal = $allSoal[$nomor - 1];
        $data['soal'] = $soal;

        // Jawaban user
        $jawabanUser = $this->tryoutjawabanModel
            ->where([
                'user_id'   => user_id(),
                'tryout_id' => $tryoutId
            ])
            ->findAll();

        $jawabanMap = [];
        foreach ($jawabanUser as $j) {
            $jawabanMap[$j['soal_id']] = $j['jawaban'];
        }

        $data['jawabanUser'] = $jawabanMap;
        $data['current'] = $nomor;
        $data['totalSoal'] = $totalSoal;

        return view('tryout/pengerjaan', $data);
    }

    public function saveJawaban()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $tryoutId = $this->request->getPost('tryout_id');
        $soalId   = $this->request->getPost('soal_id');
        $jawaban  = $this->request->getPost('jawaban');

        // VALIDASI ATTEMPT
        $attempt = $this->tryoutattemptModel
            ->where('user_id', user_id())
            ->where('tryout_id', $tryoutId)
            ->where('status', 'ongoing')
            ->first();

        if (!$attempt) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tryout terkunci'
            ]);
        }

        // UPSERT
        $existing = $this->tryoutjawabanModel
            ->where('user_id', user_id())
            ->where('tryout_id', $tryoutId)
            ->where('soal_id', $soalId)
            ->first();

        if ($existing) {
            $this->tryoutjawabanModel->update($existing['id'], [
                'jawaban'    => $jawaban,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $this->tryoutjawabanModel->insert([
                'user_id'    => user_id(),
                'tryout_id'  => $tryoutId,
                'soal_id'    => $soalId,
                'jawaban'    => $jawaban,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON(['status' => true]);
    }

    public function submit($kategori, $tryoutId)
    {
        $jawaban = $this->tryoutjawabanModel
            ->where('user_id', user_id())
            ->where('tryout_id', $tryoutId)
            ->findAll();

        $skor = 0;

        foreach ($jawaban as $j) {
            $soal = $this->tryoutSoalModel->find($j['soal_id']);
            if ($soal && $soal['jawaban_benar'] === $j['jawaban']) {
                $skor++;
            }
        }

        $attempt = $this->tryoutattemptModel
            ->where('user_id', user_id())
            ->where('tryout_id', $tryoutId)
            ->where('status', 'ongoing')
            ->first();

        if (!$attempt) {
            return redirect()->back()->with('errors', ['Session tryout tidak ditemukan']);
        }

        $this->tryoutattemptModel->update($attempt['id'], [
            'status'      => 'finished',
            'skor_akhir'  => $skor,
            'finished_at'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(site_url('/tryout/' . $kategori . '/hasil/' . $tryoutId));
    }

    public function hasil($kategori, $tryoutId)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $tryout = $this->tryoutModel->find($tryoutId);

        if (!$tryout) {
            return redirect()->back()->with('errors', ['Tryout tidak ditemukan']);
        }

        $jawaban = $this->tryoutjawabanModel
            ->where('user_id', user_id())
            ->where('tryout_id', $tryoutId)
            ->findAll();

        $totalSoal = $this->tryoutSoalModel
            ->where('tryout_id', $tryoutId)
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

        return view('tryout/hasil', $data);
    }

    public function publish($kategori, $id)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $this->tryoutModel->update($id, [
            'status' => 'aktif' // atau 'nonaktif'
        ]);

        return redirect()->back()->with('success', 'Try out berhasil diaktifkan');
    }

    public function unpublish($kategori, $id)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $this->tryoutModel->update($id, [
            'status' => 'draft' // atau 'nonaktif'
        ]);

        return redirect()->back()->with('success', 'Try out berhasil dinonaktifkan');
    }
}
