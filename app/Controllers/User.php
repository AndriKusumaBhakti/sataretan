<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserPaketModel;
use App\Models\PaketModel;
use App\Models\PaketApproveHistory;
use App\Models\ParameterModel;
use DateTime;

class User extends BaseController
{
    protected array $menuItems = [];
    protected $userModel;
    protected $userPaketModel;
    protected $tryoutModel;
    protected $paketModel;
    protected $paketApproveHistory;
    protected $parameter;

    public function __construct()
    {
        helper('auth');

        $this->menuItems = user_menu();
        $this->userModel = new UserModel();
        $this->userPaketModel = new UserPaketModel();
        $this->paketModel = new PaketModel();
        $this->paketApproveHistory = new PaketApproveHistory();
        $this->parameter = new ParameterModel();
    }

    private function baseData(): array
    {
        $data = default_parser_item([]); // Pastikan helper ini tersedia
        $data['menuItems'] = $this->menuItems;
        $data['isAdmin'] = isAdmin();
        $data['isSuperAdmin'] = isSuperAdmin();
        return $data;
    }

    public function index($kategori)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        if ($kategori == 'siswa') {
            $data['users'] = $this->userModel->bySiswa()->findAll();
        } else {
            $data['users'] = $this->userModel->byGuru()->findAll();
        }
        return view('master-data/index', $data);
    }

    public function approve($kategori, $id)
    {
        // 1. Validasi user
        $userQuery = $this->userModel
            ->where('id', $id);

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $userQuery->where('company_id', companyId());
        }

        $user = $userQuery->first();

        if (!$user) {
            return redirect()->back()->with('errors', 'User tidak ditemukan');
        }

        // 2. Ambil paket user berdasarkan user_id
        $userPaket = $this->userPaketModel
            ->where('user_id', $id)
            ->first();

        if (!$userPaket) {
            return redirect()->back()->with('errors', 'Paket user tidak ditemukan');
        }

        // 3. Ambil paket aktif
        $paket = $this->paketModel
            ->where('id', $userPaket['paket_id'])
            ->where('is_active', 1)
            ->first();

        if (!$paket) {
            return redirect()->back()->with('errors', 'Paket tidak aktif atau tidak tersedia');
        }

        // 4. Hitung expired (detik)
        $expiredAt = (new DateTime())
            ->modify('+' . (int) $paket['range_month'] . ' months')
            ->format('Y-m-d');

        // 5. Update status paket user
        $this->userPaketModel->update($userPaket['id'], [
            'status'     => 'A',
            'expired_at' => $expiredAt,
        ]);

        $this->paketApproveHistory->insert([
            'user_id'     => $id,
            'paket_id'    => $userPaket['paket_id'],
            'approved_by' => user_id(),
            'approved_at' => date('Y-m-d H:i:s'),
            'expired_at'  => $expiredAt,
        ]);
        return redirect()->back()->with('success', 'User berhasil diaktifkan');
    }

    public function create($kategori)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        if ($kategori == 'siswa') {
            $paket = $this->paketModel
                ->where('is_active', 1)
                ->when(!isSuperAdmin(), function ($query) {
                    $query->where('company_id', companyId());
                })
                ->findAll();
            $data['paket'] = $paket;
        }
        $data['program'] = $this->parameter->getValue("program");
        return view('master-data/create', $data);
    }

    // ================= SIMPAN DATA GURU =================
    public function store($kategori)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $path = WRITEPATH . 'uploads/profile';
        if ($kategori == 'siswa') {
            $rules = [
                'name' => 'required|min_length[3]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'phone' => 'required|min_length[10]',
                'program' => 'required',
                'paket_id' => 'required',
                'password' => 'required|min_length[6]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->with('errors', $this->validator->getErrors());
            }

            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                // === UPLOAD FOTO ===
                $photoName = '';
                $photo = $this->request->getFile('photo');
                if ($photo && $photo->isValid() && !$photo->hasMoved()) {
                    $photoName = $photo->getRandomName();
                    $photo->move($path, $photoName);
                }
                // INSERT USER + AMBIL ID
                $userId = $this->userModel->insert([
                    'company_id'   => companyId(),
                    'name'       => $this->request->getPost('name'),
                    'email'      => $this->request->getPost('email'),
                    'phone'      => $this->request->getPost('phone'),
                    'password'   => md5($this->request->getPost('password')),
                    'photo'      => $photoName,
                    'role_id'       => 3,
                    'created_at' => date('Y-m-d H:i:s')
                ], true);
                if (!$userId) {
                    $db->transRollback();
                    return redirect()->back()->with('errors', $this->userModel->errors());
                }

                // INSERT USER_PAKET
                $this->userPaketModel->insert([
                    'user_id'  => $userId,
                    'program'   => $this->request->getPost('program'),
                    'paket_id' => $this->request->getPost('paket_id'),
                    'status'   => 'P',
                ]);

                if ($db->transStatus() === false) {
                    $db->transRollback();
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['Gagal menyimpan paket user']);
                }

                $db->transCommit();
            } catch (\Throwable $e) {
                $db->transRollback();

                return redirect()->back()
                    ->with('errors', ['Terjadi kesalahan sistem']);
            }
        } else {
            $rules = [
                'name'     => 'required|min_length[3]',
                'email'    => 'required|valid_email|is_unique[users.email]',
                'phone'    => 'required|min_length[10]',
                'password' => 'required|min_length[6]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }
            // === UPLOAD FOTO ===
            $photoName = '';
            $photo = $this->request->getFile('photo');
            if ($photo && $photo->isValid() && !$photo->hasMoved()) {
                $photoName = $photo->getRandomName();
                $photo->move($path, $photoName);
            }

            // === SIMPAN KE DATABASE ===
            $this->userModel->insert([
                'company_id'   => companyId(),
                'name'       => $this->request->getPost('name'),
                'email'      => $this->request->getPost('email'),
                'phone'      => $this->request->getPost('phone'),
                'password'   => md5($this->request->getPost('password')),
                'photo'      => $photoName,
                'role_id'       => 2,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to('master-data/' . $kategori)
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit($kategori, $id)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;

        // Ambil user + role
        $userQuery = $this->userModel->withRoleById($id);

        $user = $userQuery->first();

        if (!$user) {
            return redirect()->back()->with('errors', 'User tidak ditemukan');
        }

        $data['user'] = $user;

        // ================= KHUSUS SISWA =================
        if ($kategori === 'siswa') {

            // Ambil paket user
            $userPaket = $this->userPaketModel
                ->where('user_id', $id)
                ->first();

            if (!$userPaket) {
                return redirect()->back()->with('errors', 'Paket user tidak ditemukan');
            }

            $data['userPaket'] = $userPaket;

            // Ambil daftar paket aktif
            $data['paket'] = $this->paketModel
                ->where('is_active', 1)
                ->findAll();
        }
        $data['program'] = $this->parameter->getValue("program");

        return view('master-data/edit', $data);
    }

    public function delete($kategori, $id)
    {
        $userQuery = $this->userModel
            ->where('id', $id);

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $userQuery->where('company_id', companyId());
        }

        $user = $userQuery->first();

        if (!$user) {
            return redirect()->back()
                ->with('errors', 'User tidak ditemukan');
        }

        // Hapus foto jika ada
        if (!empty($user['photo'])) {
            $path = WRITEPATH . 'uploads/profile/' . $user['photo'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        // Hapus relasi paket (khusus siswa)
        if ($kategori === 'siswa') {
            $this->userPaketModel
                ->where('user_id', $id)
                ->delete();
        }

        // Hapus user
        $this->userModel->delete($id);

        return redirect()->to(base_url('master-data/' . $kategori))->with('success', 'Data siswa berhasil dihapus');
    }

    public function update($kategori, $id)
    {
        $userQuery = $this->userModel
            ->where('id', $id);

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $userQuery->where('company_id', companyId());
        }

        $user = $userQuery->first();

        if (! $user) {
            return redirect()->back()
                ->with('errors', 'Data user tidak ditemukan');
        }
        // ================= VALIDASI =================
        $rules = [
            'name'  => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[users.email,id,$id]",
            'phone' => 'permit_empty',
        ];
        if ($kategori == "siswa") {
            // ================= VALIDASI =================
            $rules = [
                'name'  => 'required|min_length[3]',
                'email' => "required|valid_email|is_unique[users.email,id,$id]",
                'phone' => 'permit_empty',
                'paket_id' => 'required',
                'program' => 'required',
            ];
        }

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // ================= DATA UPDATE =================
        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ];

        // ================= UPDATE USER =================
        $this->userModel->update($id, $data);

        // ================= KHUSUS SISWA =================
        if ($kategori === 'siswa') {

            // Update paket (jika ada input)
            if ($this->request->getPost('paket_id')) {
                $this->userPaketModel
                    ->where('user_id', $id)
                    ->set([
                        'program' => $this->request->getPost('program'),
                        'paket_id' => $this->request->getPost('paket_id'),
                        'expired_at' => null,
                        'status' => "P"
                    ])
                    ->update();
            }
        }

        return redirect()->to(base_url('master-data/' . $kategori))
            ->with('success', 'Data berhasil diperbarui');
    }
}
