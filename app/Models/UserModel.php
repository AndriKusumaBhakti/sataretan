<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $allowedFields = [
        'company_id',
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'photo',
        'created_at',
        'updated_at'
    ];

    public function withRole()
    {
        return $this->select('users.*, roles.name AS role')
            ->join('roles', 'roles.id = users.role_id');
    }

    public function withRoleById($id)
    {
        $query =  $this->select('users.id as id, users.*, roles.name AS role')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.id', $id);
        if (!isSuperAdmin()) {
            $query->where('users.company_id', companyId());
        }
        return $query;
    }

    public function bySiswa()
    {
        $query = $this->select('users.*, user_paket.expired_at AS paket_exp, user_paket.status AS paket_status, user_paket.program AS user_program, paket.nama AS name_paket, paket.deskripsi AS paket_desc')
            ->join('user_paket', 'user_paket.user_id = users.id')
            ->join('paket', 'paket.id = user_paket.paket_id');
        if (!isSuperAdmin()) {
            $query->where('users.company_id', companyId());
        }

        return $query;
    }

    public function byGuru()
    {
        $query = $this->select('users.*, roles.name AS role')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.role_id', '2');
        if (!isSuperAdmin()) {
            $query->where('users.company_id', companyId());
        }

        return $query;
    }
}
