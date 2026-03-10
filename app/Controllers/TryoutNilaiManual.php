<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TryoutModel;
use App\Models\TryoutSoalModel;
use App\Models\TryoutJawabanModel;
use App\Models\TryoutAttemptModel;
use App\Models\UserPaketModel;
use App\Models\ParameterModel;

class TryoutNilaiManual extends BaseController
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

    public function tambah($kategori, $tryoutId)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;

        $tryoutQuery = $this->tryoutModel->where('id', $tryoutId);

        if (!isSuperAdmin()) {
            $tryoutQuery->where('company_id', companyId());
        }

        $tryout = $tryoutQuery->first();

        if (!$tryout) {
            return redirect()->back()->with('errors', ['Tryout tidak ditemukan']);
        }

        // program tryout
        $program = json_decode($tryout['program'], true) ?? [];

        // ujian tryout
        $ujianTryout = json_decode($tryout['ujian'], true) ?? [];

        // ======================
        // ambil tryout_cabang
        // ======================
        $builder = $this->db->table('tryout_cabang');

        if (!isSuperAdmin()) {
            $builder->where('company_id', companyId());
        }

        $rows = $builder
            ->where('category', $kategori)
            ->whereIn('key', $ujianTryout)
            ->get()
            ->getResultArray();

        // ======================
        // tentukan jenis penilaian
        // ======================
        $jenisPenilaian = 'angka'; // default

        if (!empty($rows)) {

            $modes = [];

            foreach ($rows as $r) {
                $modes[] = $r['penilaian_type'];
            }

            $modes = array_unique($modes);

            if (count($modes) === 1) {
                $jenisPenilaian = $modes[0];
            } else {
                $jenisPenilaian = 'keduanya';
            }
        }

        // ======================
        // user peserta
        // ======================
        $user = $this->userPaketModel
            ->getAllUserAktifByProgram($program);

        $data['users'] = $user;
        $data['tryout'] = $tryout;
        $data['tryoutId'] = $tryoutId;
        $data['jenisPenilaian'] = $jenisPenilaian;
        $data['ujianTryout'] = $ujianTryout;

        return view('tryout/nilai/tambah', $data);
    }

    public function simpan($kategori, $tryoutId)
    {
        $userId = $this->request->getPost('user_id');

        // ======================
        // VALIDASI USER
        // ======================
        if (!$userId) {
            return redirect()->back()
                ->withInput()
                ->with('errors', 'Peserta wajib dipilih');
        }

        // ======================
        // CEK DUPLIKASI NILAI
        // ======================
        $exists = $this->tryoutattemptModel
            ->where('tryout_id', $tryoutId)
            ->where('user_id', $userId)
            ->first();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('errors', 'Peserta sudah memiliki nilai untuk tryout ini');
        }

        // ======================
        // AMBIL INPUT DEFAULT
        // ======================
        $skor = $this->request->getPost('skor_akhir');
        $deskripsi = $this->request->getPost('deskripsi_nilai');

        // ======================
        // TES KORAN (TI)
        // ======================
        if ($this->request->getPost('tempo') !== null) {

            $deskripsi = json_encode([
                'tempo' => $this->request->getPost('tempo'),
                'akurasi' => $this->request->getPost('akurasi'),
                'konsisten' => $this->request->getPost('konsisten'),
                'daya_tahan' => $this->request->getPost('daya_tahan'),
                'adaptasi' => $this->request->getPost('adaptasi'),
                'disiplin' => $this->request->getPost('disiplin'),
                'tekanan' => $this->request->getPost('tekanan'),
                'emosi' => $this->request->getPost('emosi'),
            ]);

            $skor = null;
        }

        // ======================
        // TES GAMBAR (TG)
        // ======================
        elseif ($this->request->getPost('tanggung_jawab') !== null) {

            $deskripsi = json_encode([
                'tanggung_jawab' => $this->request->getPost('tanggung_jawab'),
                'kontrol_diri' => $this->request->getPost('kontrol_diri'),
                'keseimbangan_sosial' => $this->request->getPost('keseimbangan_sosial'),
                'ekspresi_diri' => $this->request->getPost('ekspresi_diri'),
                'emosi' => $this->request->getPost('emosi'),
            ]);

            $skor = null;
        }

        // ======================
        // MENTAL IDEOLOGI (MI)
        // ======================
        elseif ($this->request->getPost('tulis') !== null) {

            $deskripsi = json_encode([
                'nilai_tulis' => $this->request->getPost('tulis'),
                'penguasaan_materi' => $this->request->getPost('materi'),
                'kecakapan' => $this->request->getPost('kecakapan'),
                'kedisiplinan' => $this->request->getPost('kedisiplinan'),
            ]);

            $skor = null;
        }

        // ======================
        // VALIDASI NILAI
        // ======================
        $skor = ($skor !== '' && $skor !== null) ? $skor : null;
        $deskripsi = ($deskripsi !== '' && $deskripsi !== null) ? $deskripsi : null;

        if (is_null($skor) && is_null($deskripsi)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', 'Isi skor atau deskripsi minimal salah satu.');
        }

        // ======================
        // INSERT DATA
        // ======================
        $this->tryoutattemptModel->insert([
            'tryout_id' => $tryoutId,
            'user_id' => $userId,
            'skor_akhir' => $skor,
            'deskripsi_nilai' => $deskripsi,
            'status' => 'finished',
            'started_at' => date('Y-m-d H:i:s'),
            'finished_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()
            ->to("tryout/$kategori/nilai/$tryoutId")
            ->with('success', 'Nilai berhasil ditambahkan');
    }
}
