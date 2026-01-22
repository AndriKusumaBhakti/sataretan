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
            'kategori'      => 'required|in_list[tni,polri]',
            'jenis_kelamin' => 'required|in_list[pria,wanita]',

            'lari_12'       => 'permit_empty|numeric',
            'pull_up'     => 'permit_empty|numeric',
            'sit_up'      => 'permit_empty|numeric',
            'push_up'     => 'permit_empty|numeric',
            'shuttle_run' => 'permit_empty|numeric',
        ];

        // KHUSUS TNI
        if ($kategori === 'tni') {
            $rules['lunges']   = 'required|numeric';
            $rules['usia']   = 'required|numeric';
            $rules['tinggi'] = 'required|numeric';
            $rules['berat']  = 'required|numeric';
        }

        // KHUSUS POLRI
        if ($kategori === 'polri') {
            $rules['renang'] = 'required|numeric';
        }

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

        $data = [
            'user_id'       => $userId,
            'kategori'      => $kategori,
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),

            // TNI
            'usia'          => $this->request->getPost('usia'),
            'tinggi_cm'     => $this->request->getPost('tinggi'),
            'berat_kg'      => $this->request->getPost('berat'),
            'bmi_index'     => $this->request->getPost('nilai_bmi'),
            'bmi_kategori'  => $this->request->getPost('kategori_bmi'),

            // GARJAS
            'lari_12'       => $this->request->getPost('lari_12'),
            'nilai_lari_12' => $this->request->getPost('nilai_lari_12'),

            'pull_up'       => $this->request->getPost('pull_up'),
            'nilai_pull_up' => $this->request->getPost('nilai_pull_up'),

            'sit_up'        => $this->request->getPost('sit_up'),
            'nilai_sit_up'  => $this->request->getPost('nilai_sit_up'),

            'lunges'        => $this->request->getPost('lunges'),
            'nilai_lunges'  => $this->request->getPost('nilai_lunges'),

            'push_up'       => $this->request->getPost('push_up'),
            'nilai_push_up' => $this->request->getPost('nilai_push_up'),

            'shuttle_run'        => $this->request->getPost('shuttle_run'),
            'nilai_shuttle_run' => $this->request->getPost('nilai_shuttle_run'),

            'renang'       => $this->request->getPost('renang'),
            'nilai_renang' => $this->request->getPost('nilai_renang'),
        ];

        // HITUNG NILAI

        $data['nilai_garjas_b'] = $this->hitungGarjasB($data);
        $data['nilai_total']    = $this->hitungTotal($data);

        $this->jasmani->insert($data);

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

    private function hitungGarjasB(array $data)
    {
        $nilai = [
            $data['nilai_pull_up'],
            $data['nilai_sit_up'],
            $data['nilai_lunges'],
            $data['nilai_push_up'],
            $data['nilai_shuttle_run'],
        ];

        if (!empty($data['nilai_renang'])) {
            $nilai[] = $data['nilai_renang'];
        }

        $nilai = array_filter($nilai, 'is_numeric');

        return count($nilai)
            ? round(array_sum($nilai) / count($nilai), 2)
            : null;
    }

    private function hitungTotal(array $data)
    {
        $nilai = array_filter([
            $data['nilai_lari_12'],
            $data['nilai_garjas_b'],
        ], 'is_numeric');

        return count($nilai)
            ? round(array_sum($nilai) / count($nilai), 2)
            : null;
    }
}
