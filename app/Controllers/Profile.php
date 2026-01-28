<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profile extends BaseController
{
    protected array $menuItems = [];
    protected $userModel;

    public function __construct()
    {
        helper('auth');

        $this->menuItems = user_menu();
        $this->userModel = new UserModel();
    }

    private function baseData(): array
    {
        $this->checkDatabase();
        $data = default_parser_item([]); // Pastikan helper ini tersedia
        $data['menuItems'] = $this->menuItems;
        $data['base_url'] = base_url('/');
        return $data;
    }

    public function index()
    {
        $data = $this->baseData();

        $userQuery = $this->userModel
            ->where('id', user_id());

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $userQuery->where('company_id', companyId());
        }

        $user = $userQuery->first();

        if (!$user) {
            return redirect()->back()->with('errors', 'User tidak ditemukan');
        }

        $data['user'] = $user;
        
        return view('profile/index', $data);
    }

    public function settings()
    {
        $data = $this->baseData();
        return view('profile/index', $data);
        return view('profile/settings');
    }

    public function edit()
    {
        $data = $this->baseData();
        $userQuery = $this->userModel
            ->where('id', user_id());

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $userQuery->where('company_id', companyId());
        }

        $user = $userQuery->first();

        if (!$user) {
            return redirect()->back()->with('errors', 'User tidak ditemukan');
        }

        $data['user'] = $user;

        return view('profile/edit', $data);
    }

    public function update()
    {
        $userQuery = $this->userModel
            ->where('id', user_id());

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $userQuery->where('company_id', companyId());
        }

        $user = $userQuery->first();

        if (!$user) {
            return redirect()->back()->with('errors', 'User tidak ditemukan');
        }

        $data = $this->baseData();
        $data['name'] = $this->request->getPost('name');
        $data['email'] = $this->request->getPost('email');
        $data['phone'] = $this->request->getPost('phone');

        $data_user = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ];

        // upload foto
        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {

            $newName = $file->getRandomName();
            $path = WRITEPATH . 'uploads/profile';
            $file->move($path, $newName);

            $data_user['photo'] = $newName;

            // update session photo
            session()->set('photo', $newName);
        }

        $this->userModel->update($user['id'], $data_user);

        // update session name
        session()->set('name', $data['name']);

        return redirect()->to(site_url('profile'))->with('success', 'Profile berhasil diperbarui');
    }

    public function accountSettings()
    {
        $data = $this->baseData();

        $userQuery = $this->userModel
            ->where('id', user_id());

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $userQuery->where('company_id', companyId());
        }

        $user = $userQuery->first();

        if (!$user) {
            return redirect()->back()->with('errors', 'User tidak ditemukan');
        }

        $data['user'] = $user;
        return view('profile/account-settings', $data);
    }

    public function changePassword()
    {
        $rules = [
            'old_password' => 'required',
            'new_password'     => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userQuery = $this->userModel
            ->where('id', user_id());

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $userQuery->where('company_id', companyId());
        }

        $user = $userQuery->first();

        if (!$user) {
            return redirect()->back()->with('errors', 'User tidak ditemukan');
        }

        // Validasi password lama
        if (md5($this->request->getPost('old_password')) != $user['password']) {
            return redirect()->back()->with('errors', ['Password lama tidak sesuai']);
        }

        // Update password baru
        $this->userModel->update(user_id(), [
            'password' => md5($this->request->getPost('new_password')),
        ]);

        return redirect()->back()->with('success', 'Password berhasil diperbarui');
    }
}
