<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $allowedFields = [
        'name','email','phone','password','role_id', 'photo', 'created_at', 'updated_at'
    ];

    public function withRole()
    {
        return $this->select('users.*, roles.name AS role')
            ->join('roles','roles.id = users.role_id');
    }

    public function bySiswa()
    {
        return $this->select('users.*, user_paket.expired_at AS paket_exp, user_paket.status AS paket_status, user_paket.program AS user_program, paket.nama AS name_paket, paket.deskripsi AS paket_desc')
            ->join('user_paket','user_paket.user_id = users.id')
            ->join('paket','paket.id = user_paket.paket_id');
    }

    public function byGuru()
    {
        return $this->select('users.*, roles.name AS role')
            ->join('roles','roles.id = users.role_id')
            ->where('users.role_id','2');
    }
}
