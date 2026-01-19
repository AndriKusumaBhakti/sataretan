<?php

namespace App\Controllers;

use App\Models\JasmaniModel;
use App\Models\UserPaketModel;

class Jasmani extends BaseController
{
    protected $jasmani;
    protected array $menuItems = [];
    protected $userPaketModel;

    public function __construct()
    {
        helper('auth');
        $this->menuItems = user_menu();
        $this->jasmani = new JasmaniModel();
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

    public function index($kategori)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;

        $user = $this->userPaketModel->getAllUserAktif();
        $data['users'] = $user;
        $jasmani = $this->jasmani->getAllByUser();
        $data['jasmani'] = $jasmani;

        return view('tryout/jasmani/index', $data);
    }

    public function create($kategori)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;

        $user = $this->userPaketModel->getAllUserAktif();
        $data['users'] = $user;

        return view('tryout/jasmani/tambah', $data);
    }

    public function store($kategori)
    {
        $rules = [
            'kategori' => 'required|in_list[tni,polri]',
            'jenis_kelamin' => 'required|in_list[Pria,Wanita]',
            'lari_12' => 'required|numeric',

            // Garjas B (boleh 0 tapi tetap numeric)
            'pull_up' => 'permit_empty|numeric',
            'sit_up' => 'permit_empty|numeric',
            'lunges' => 'permit_empty|numeric',
            'push_up' => 'permit_empty|numeric',
            'shuttle_run' => 'permit_empty|numeric',
            'renang' => 'permit_empty|numeric',
        ];

        if (isGuruOrAdmin()) {
            $rules['user_id'] = 'required|is_not_unique[users.id]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        $userId = isGuruOrAdmin()
            ? $this->request->getPost('user_id')
            : user_id(); // helper user login
        $this->jasmani->insert([
            'user_id'        => $userId,
            'kategori'       => $this->request->getPost('kategori'),
            'jenis_kelamin'  => $this->request->getPost('jenis_kelamin'),
            'usia'           => $this->request->getPost('usia'),
            'tinggi'         => $this->request->getPost('tinggi'),
            'berat'          => $this->request->getPost('berat'),
            'lari_12'        => $this->request->getPost('lari_12'),
            'pull_up'        => $this->request->getPost('pull_up'),
            'sit_up'         => $this->request->getPost('sit_up'),
            'lunges'         => $this->request->getPost('lunges'),
            'push_up'        => $this->request->getPost('push_up'),
            'shuttle_run'    => $this->request->getPost('shuttle_run'),
            'renang'         => $this->request->getPost('renang'),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('tryout/' . $kategori))->with('success', 'Data jasmani berhasil disimpan');
    }

    public function detail($kategori, $id)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $data['jasmani'] = $this->jasmani
            ->select('jasmani.*, users.name, users.email')
            ->join('users', 'users.id = jasmani.user_id', 'left')
            ->where('jasmani.id', $id)
            ->first();

        if (!$data['jasmani']) {
            return redirect()->back()->with('errors', ['Data tidak ditemukan']);
        }

        return view('tryout/jasmani/detail', $data);
    }

    public function delete($kategori, $id)
    {
        $this->jasmani->delete($id);
        return redirect()->to(site_url('tryout/' . $kategori))->with('success', 'Data jasmani berhasil dihapus');
    }
}
