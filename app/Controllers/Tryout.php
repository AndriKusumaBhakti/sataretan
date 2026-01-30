<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TryoutModel;
use App\Models\TryoutSoalModel;
use App\Models\TryoutJawabanModel;
use App\Models\TryoutAttemptModel;
use App\Models\UserPaketModel;
use App\Models\ParameterModel;

class Tryout extends BaseController
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

    private function isValidDate($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    // halaman list try out
    public function index($kategori)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        if ($kategori == "jasmani") {
            return redirect()->to(site_url('tryout/' . $kategori . '/view'));
        }
        if (!isGuruOrAdmin()) {
            $user = $this->userPaketModel
                ->select('program')
                ->where('user_id', user_id())
                ->first();

            $userProgram = $user['program'] ?? null;

            if (! $userProgram) {
                $data['tryout'] = [];
            } else {
                $data['tryout'] = $this->tryoutModel->getStatistikSiswaByProgram($userProgram, $kategori);
            }
        } else {
            $data['tryout'] = $this->tryoutModel->getTryoutStatistik($kategori);
        }

        return view('tryout/index', $data);
    }

    // halaman mulai try out
    public function start($kategori, $id)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;

        $tryoutQuery = $this->tryoutModel
            ->where('id', $id);

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $tryoutQuery->where('company_id', companyId());
        }

        $tryout = $tryoutQuery->first();

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

        $data['pilihan'] = $this->parameter->getValue($kategori);
        $data['filterProgram'] = $this->parameter->getValue("filter_program");
        $data['program'] = $this->parameter->getValue("program");

        return view('tryout/tryout/tambah', $data);
    }

    public function simpan($kategori)
    {
        $rules = [
            'program' => 'required',
            'pilihan'       => 'required',
            'judul'   => 'required|min_length[3]',
            'jumlah_soal'    => 'permit_empty|numeric',
            'durasi'  => 'permit_empty|numeric',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $tanggalMulai   = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');

        if (!$this->isValidDate($tanggalMulai) || !$this->isValidDate($tanggalSelesai)) {
            return redirect()->back()
                ->with('errors', ['Format tanggal tidak valid'])
                ->withInput();
        }

        // ❌ VALIDASI URUTAN TANGGAL
        if ($tanggalSelesai < $tanggalMulai) {
            return redirect()->back()
                ->with('errors', ['Tanggal selesai tidak boleh lebih awal dari tanggal mulai'])
                ->withInput();
        }

        $programArray = $this->request->getPost('program');
        $programJson  = json_encode($programArray);

        $pilihanArray = $this->request->getPost('pilihan');
        $pilihanJson  = json_encode($pilihanArray);

        $this->tryoutModel->insert([
            'company_id'   => companyId(),
            'kategori'     => $kategori,
            'program'  => $programJson,
            'ujian'       => $pilihanJson,
            'judul'        => $this->request->getPost('judul'),
            'jumlah_soal'  => $this->request->getPost('jumlah_soal'),
            'durasi'       => $this->request->getPost('durasi'),
            'tanggal_mulai'       => $tanggalMulai,
            'tanggal_selesai'       => $tanggalSelesai,
            'status'       => 'draft',
        ]);

        return redirect()->to(site_url('tryout/' . $kategori))->with('success', 'Try Out berhasil ditambahkan');
    }

    public function edit($kategori, $id)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $tryoutQuery = $this->tryoutModel
            ->where('id', $id);

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $tryoutQuery->where('company_id', companyId());
        }

        $tryout = $tryoutQuery->first();

        if (!$tryout) {
            return redirect()->back()->with('errors', 'Try Out tidak ditemukan');
        }

        $data['pilihan'] = $this->parameter->getValue($kategori);
        $data['filterProgram'] = $this->parameter->getValue("filter_program");
        $data['program'] = $this->parameter->getValue("program");
        $data['tryout'] = $tryout;
        return view('tryout/tryout/edit', $data);
    }

    public function update($kategori, $id)
    {
        $rules = [
            'program' => 'required',
            'pilihan'       => 'required',
            'judul'   => 'required|min_length[3]',
            'jumlah_soal'    => 'permit_empty|numeric',
            'durasi'  => 'permit_empty|numeric',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $tanggalMulai   = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');

        if (!$this->isValidDate($tanggalMulai) || !$this->isValidDate($tanggalSelesai)) {
            return redirect()->back()
                ->with('errors', ['Format tanggal tidak valid'])
                ->withInput();
        }

        // ❌ VALIDASI URUTAN TANGGAL
        if ($tanggalSelesai < $tanggalMulai) {
            return redirect()->back()
                ->with('errors', ['Tanggal selesai tidak boleh lebih awal dari tanggal mulai'])
                ->withInput();
        }

        $programArray = $this->request->getPost('program'); // ['tni','polri']
        $programJson  = json_encode($programArray);

        $pilihanArray = $this->request->getPost('pilihan');
        $pilihanJson  = json_encode($pilihanArray);

        $this->tryoutModel->update($id, [
            'company_id'   => companyId(),
            'program'  => $programJson,
            'ujian'       => $pilihanJson,
            'judul'       => $this->request->getPost('judul'),
            'jumlah_soal' => $this->request->getPost('jumlah_soal'),
            'durasi'      => $this->request->getPost('durasi'),
            'tanggal_mulai'       => $tanggalMulai,
            'tanggal_selesai'       => $tanggalSelesai,
        ]);

        return redirect()->to(site_url('tryout/' . $kategori));
    }

    // ================= DELETE =================
    public function delete($kategori, $id)
    {
        $tryoutQuery = $this->tryoutModel
            ->where('id', $id)
            ->where('kategori', $kategori);

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $tryoutQuery->where('company_id', companyId());
        }

        $tryout = $tryoutQuery->first();

        if (!$tryout) {
            return redirect()->back()->with('errors', ['Tryout tidak ditemukan']);
        }
        $this->db->transStart();

        try {
            $this->tryoutjawabanModel
                ->where('tryout_id', $id)
                ->delete();

            $this->tryoutattemptModel
                ->where('tryout_id', $id)
                ->delete();

            $this->tryoutSoalModel
                ->where('tryout_id', $id)
                ->delete();
            $this->tryoutModel->delete($id);

            $this->db->transComplete();

            return redirect()->back()
                ->with('success', 'Tryout berhasil dihapus');
        } catch (\Throwable $e) {

            $this->db->transRollback();

            return redirect()->back()
                ->with('errors', 'Gagal menghapus tryout');
        }
    }

    public function pengerjaan($kategori, $tryoutId, $nomor = 1)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;

        // Ambil tryout
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
        $now = time();
        $tanggalSelesai = strtotime($tryout['tanggal_selesai'] . ' 23:59:59');

        if ($now > $tanggalSelesai) {
            // paksa submit jawaban
            return redirect()->to(
                site_url('tryout/' . $kategori . '/submit/' . $tryoutId)
            )->with('errors', 'Waktu try out telah berakhir');
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

        $soalList = $this->tryoutSoalModel->where('tryout_id', $tryoutId)->findAll();

        $totalSoal = count($soalList);

        $benar = 0;
        $detail = [];
        $nilai = 0;
        $allNilaiZero = true; // flag untuk cek semua nilai opsi = 0
        foreach ($soalList as $soal) {
            // cek semua nilai opsi
            foreach (['A', 'B', 'C', 'D', 'E'] as $opsi) {
                if (($soal['nilai_' . $opsi] ?? 0) > 0) {
                    $allNilaiZero = false;
                    break 2; // langsung stop jika ada >0
                }
            }
        }

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

            $nilai_soal = 0;
            foreach (['A', 'B', 'C', 'D', 'E'] as $opsi) {
                $nilai_opsi = isset($soal['nilai_' . $opsi]) ? (float)$soal['nilai_' . $opsi] : 0;

                if ($jawaban_user === strtoupper($opsi)) {
                    $nilai_soal += $nilai_opsi;
                }
            }

            $nilai += $nilai_soal;

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
        if ($allNilaiZero) {
            $nilai = $totalSoal > 0 ? round(($benar / $totalSoal) * 100, 2) : 0;
        }

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

        $attempt = $this->tryoutattemptModel
            ->where('tryout_id', $id)
            ->where('status', 'ongoing')
            ->findAll();

        if ($attempt) {
            foreach ($attempt as $a) {
                $jawaban = $this->tryoutjawabanModel
                    ->where('user_id', $a['user_id'])
                    ->where('tryout_id', $id)
                    ->findAll();

                $skor = 0;

                foreach ($jawaban as $j) {
                    $soal = $this->tryoutSoalModel->find($j['soal_id']);
                    if ($soal && $soal['jawaban_benar'] === $j['jawaban']) {
                        $skor++;
                    }
                }

                $this->tryoutattemptModel->update($a['id'], [
                    'status'      => 'finished',
                    'skor_akhir'  => $skor,
                    'finished_at'  => date('Y-m-d H:i:s')
                ]);
            }
        }

        return redirect()->back()->with('success', 'Try out berhasil dinonaktifkan');
    }
}
