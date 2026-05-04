<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ParameterModel;
use App\Models\UserModel;
use App\Models\UserPaketModel;
use App\Models\PaketModel;
use App\Models\PaketApproveHistory;

class Parameter extends BaseController
{
    protected array $menuItems = [];
    protected $parameterModel;
    protected $userModel;
    protected $userPaketModel;
    protected $paketModel;
    protected $paketApproveHistory;

    public function __construct()
    {
        helper('auth');

        $this->menuItems = user_menu();
        $this->parameterModel = new ParameterModel();
    }

    private function baseData(): array
    {
        $this->checkDatabase();

        $data = default_parser_item([]);
        $data['menuItems'] = $this->menuItems;
        $data['base_url']  = base_url('/');
        $this->userModel = new UserModel();
        $this->userPaketModel = new UserPaketModel();
        $this->paketModel = new PaketModel();
        $this->paketApproveHistory = new PaketApproveHistory();

        return $data;
    }

    public function index()
    {
        if (!isSuperAdmin()) {
            return redirect()->back()->with('errors', ['Akses ditolak']);
        }

        $data = $this->baseData();

        $data['params'] = $this->parameterModel
            ->orderBy('id', 'ASC')
            ->findAll();

        return view('parameter/index', $data);
    }

    public function create()
    {
        if (!isSuperAdmin()) {
            return redirect()->back()->with('errors', ['Akses ditolak']);
        }

        $data = $this->baseData();

        return view('parameter/create', $data);
    }

    public function store()
    {
        if (!isSuperAdmin()) {
            return redirect()->back()->with('errors', ['Akses ditolak']);
        }

        $rules = [
            'code'  => 'required|min_length[3]|is_unique[parameter.code]',
            'value' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $value = $this->request->getPost('value');

        // Validasi JSON
        json_decode($value);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['Value harus berupa JSON valid']);
        }

        $this->parameterModel->insert([
            'code'  => $this->request->getPost('code'),
            'value' => $value
        ]);

        return redirect()->to(site_url('/maintenance/parameter'))
            ->with('success', 'Parameter berhasil ditambahkan');
    }

    public function edit($id)
    {
        if (!isSuperAdmin()) {
            return redirect()->back()->with('errors', ['Akses ditolak']);
        }

        $data = $this->baseData();

        $param = $this->parameterModel->find($id);

        if (!$param) {
            return redirect()->back()->with('errors', ['Parameter tidak ditemukan']);
        }

        $data['param'] = $param;

        return view('parameter/edit', $data);
    }

    public function update($id)
    {
        if (!isSuperAdmin()) {
            return redirect()->back()->with('errors', ['Akses ditolak']);
        }

        $param = $this->parameterModel->find($id);

        if (!$param) {
            return redirect()->back()->with('errors', ['Parameter tidak ditemukan']);
        }

        $rules = [
            'code'  => "required|min_length[3]|is_unique[parameter.code,id,$id]",
            'value' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $value = $this->request->getPost('value');

        // Validasi JSON
        json_decode($value);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['Value harus berupa JSON valid']);
        }

        $this->parameterModel->update($id, [
            'code'  => $this->request->getPost('code'),
            'value' => $value
        ]);

        return redirect()->to(site_url('/maintenance/parameter'))
            ->with('success', 'Parameter berhasil diperbarui');
    }

    public function delete($id)
    {
        if (!isSuperAdmin()) {
            return redirect()->back()->with('errors', ['Akses ditolak']);
        }

        $param = $this->parameterModel->find($id);

        if (!$param) {
            return redirect()->back()->with('errors', ['Parameter tidak ditemukan']);
        }

        $this->parameterModel->delete($id);

        return redirect()->to(site_url('/maintenance/parameter'))
            ->with('success', 'Parameter berhasil dihapus');
    }

    public function siswaHistory()
    {
        $data = $this->baseData();

        $filter = [
            'status'    => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to'   => $this->request->getGet('date_to'),
        ];

        // ✅ LANGSUNG AMBIL ARRAY
        $users = $this->userModel->bySiswaHistory($filter);

        $data['users']  = $users;
        $data['filter'] = $filter;

        return view('parameter/siswa-history', $data);
    }

    public function updateStatusInactive($userId)
    {
        $this->baseData();
        $userPaket = $this->userPaketModel
            ->where('user_id', $userId)
            ->first();

        if (!$userPaket) {
            return redirect()->back()->with(
                'errors',
                'Data paket user tidak ditemukan'
            );
        }

        $this->userPaketModel
            ->where('user_id', $userId)
            ->set([
                'status'      => 'I',
                'updated_at'  => date('Y-m-d H:i:s')
            ])
            ->update();
        return redirect()->back()->with(
            'success',
            'Status user berhasil diubah menjadi Tidak Aktif'
        );
    }

    public function detail($userId)
    {
        $this->baseData();
        $user = $this->userModel
            ->where('id', $userId)
            ->first();

        if (!$user) {
            return redirect()->back()->with(
                'errors',
                'Data paket user tidak ditemukan'
            );
        }

        $userPaket = $this->userPaketModel
            ->where('user_id', $userId)
            ->first();

        $history = $this->paketApproveHistory
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [
            'title'       => 'Detail History User Approve',
            'user'        => $user,
            'user_paket'  => $userPaket,
            'history'     => $history,
        ];

        return view('master-data/detail', $data);
    }

    public function siswaHistoryDetail($userId)
    {
        $db = \Config\Database::connect();

        $data = $db->table('h_paket_approve h')
            ->select('
                h.id,
                h.user_id,
                h.approved_by,
                h.created_at,
                h.expired_at,
                h.note,
                u.name as approved_name
            ')
            ->join('users u', 'u.id = h.approved_by', 'left')
            ->where('h.user_id', $userId)
            ->orderBy('h.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($data);
    }
}
