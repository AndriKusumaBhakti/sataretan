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
        $program = $this->request->getPost('program'); // tni / polri
        $gender   = $this->request->getPost('jenis_kelamin');

        $rules = [
            'program'        => 'required|in_list[tni,polri]',
            'jenis_kelamin'  => 'required|in_list[pria,wanita]',

            'lari_12'        => 'permit_empty|numeric',
            'sit_up'         => 'permit_empty|numeric',
            'push_up'        => 'permit_empty|numeric',
            'shuttle_run'    => 'permit_empty|numeric',
            'renang'         => 'permit_empty|numeric',
        ];

        // KHUSUS TNI
        if ($program === 'tni') {
            $rules['usia']   = 'required|numeric';
            $rules['tinggi'] = 'required|numeric';
            $rules['berat']  = 'required|numeric';
        }

        if ($gender === 'wanita') {
            $rules['chinning']   = 'permit_empty|numeric';
        } else {
            $rules['pull_up']   = 'permit_empty|numeric';
        }

        // PILIH USER JIKA GURU / ADMIN
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

        $garjasB = null;
        if ($program === 'tni') {
            $garjasB = $this->hitungGarjasB($this->request->getPost(), $gender);
        }
        $data = [
            'user_id'       => $userId,
            'kategori'      => $program,
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),

            // TNI
            'usia'          => $this->request->getPost('usia'),
            'tinggi'     => $this->request->getPost('tinggi'),
            'berat'      => $this->request->getPost('berat'),
            'bmi'           => $this->request->getPost('bmi'),
            'kategori_bmi'  => $this->request->getPost('kategori_bmi'),

            // GARJAS
            'lari_12'       => $this->request->getPost('lari_12'),
            'nilai_lari_12' => $this->request->getPost('nilai_lari_12'),

            'pull_up'       => $this->request->getPost('pull_up'),
            'nilai_pull_up' => $this->request->getPost('nilai_pull_up'),

            'chinning'        => $this->request->getPost('chinning'),
            'nilai_chinning'  => $this->request->getPost('nilai_chinning'),

            'sit_up'        => $this->request->getPost('sit_up'),
            'nilai_sit_up'  => $this->request->getPost('nilai_sit_up'),

            'push_up'       => $this->request->getPost('push_up'),
            'nilai_push_up' => $this->request->getPost('nilai_push_up'),

            'shuttle_run'        => $this->request->getPost('shuttle_run'),
            'nilai_shuttle_run' => $this->request->getPost('nilai_shuttle_run'),

            'renang'       => $this->request->getPost('renang'),
            'nilai_renang' => $this->request->getPost('nilai_renang'),

            'nilai_garjas_b' => $garjasB,
        ];

        $this->jasmani->insert($data);

        return redirect()->to(site_url('tryout/' . $kategori))->with('success', 'Data jasmani berhasil disimpan');
    }

    public function detail($kategori, $id)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $jasmani = $this->jasmani
            ->select('jasmani.*, users.name, users.email')
            ->join('users', 'users.id = jasmani.user_id', 'left')
            ->when(!isSuperAdmin(), function ($query) {
                $query->where('users.company_id', companyId());
            })
            ->where('jasmani.id', $id)
            ->first();

        if (!$jasmani) {
            return redirect()->back()->with('errors', ['Data tidak ditemukan']);
        }
        if ($jasmani['kategori'] === 'tni') {
            $jasmani['total_nilai'] = $this->hitungTotalTni($jasmani);
        } else {
            $jasmani['total_nilai'] = $this->hitungTotalPolri($jasmani);
        }
        
        $data['jasmani'] = $jasmani;

        return view('tryout/jasmani/detail', $data);
    }

    public function delete($kategori, $id)
    {
        $this->jasmani->delete($id);
        return redirect()->to(site_url('tryout/' . $kategori))->with('success', 'Data jasmani berhasil dihapus');
    }

    private function hitungGarjasB(array $data, $gender)
    {
        $items  = ($gender === 'pria')
            ? ['nilai_pull_up', 'nilai_sit_up', 'nilai_push_up', 'nilai_shuttle_run', 'nilai_renang']
            : ['nilai_chinning', 'nilai_sit_up', 'nilai_push_up', 'nilai_shuttle_run', 'nilai_renang'];

        $total = 0;
        $count = 0;

        foreach ($items as $item) {
            if (!empty($data[$item])) {
                $total += $data[$item];
                $count++;
            }
        }

        return $count > 0 ? round($total / $count, 2) : 0;
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
