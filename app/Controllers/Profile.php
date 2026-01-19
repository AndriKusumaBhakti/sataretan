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

        $data['user'] = $this->userModel
            ->where('id', user_id())
            ->first();
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
        $data['user'] = $this->userModel
            ->where('id', user_id())
            ->first();

        return view('profile/edit', $data);
    }

    public function update()
    {
        $data = $this->baseData();
        $data['name'] = $this->request->getPost('name');
        $data['email'] = $this->request->getPost('email');

        $data_user = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
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

        $this->userModel->update(user_id(), $data_user);

        // update session name
        session()->set('name', $data['name']);

        return redirect()->to(site_url('profile'))->with('success', 'Profile berhasil diperbarui');
    }
}
