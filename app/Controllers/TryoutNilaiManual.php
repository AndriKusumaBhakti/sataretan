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

        $user = $this->userPaketModel->getAllUserAktifByProgram(json_decode($tryout['program']));
        $data['users'] = $user;
        $data['tryout'] = $tryout;
        $data['tryoutId'] = $tryoutId;

        return view('tryout/nilai/tambah', $data);
    }

    public function simpan($kategori, $tryoutId)
    {
        $data = $this->request->getPost();

        $exists = $this->tryoutattemptModel
            ->where('tryout_id', $tryoutId)
            ->where('user_id', $data['user_id'])
            ->first();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('errors', 'Peserta sudah memiliki nilai untuk tryout ini');
        }

        $this->tryoutattemptModel->insert([
            'tryout_id'   => $tryoutId,
            'user_id'        => $data['user_id'],
            'skor_akhir'  => $data['skor_akhir'],
            'status'      => 'finished',
            'started_at'  => date('Y-m-d H:i:s'),
            'finished_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()
            ->to("tryout/$kategori/nilai/$tryoutId")
            ->with('success', 'Nilai berhasil ditambahkan');
    }
}
