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
        'reset_token',
        'reset_expired',
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
            ->join('paket', 'paket.id = user_paket.paket_id')
            ->orderBy('users.id', 'DESC');
        if (!isSuperAdmin()) {
            $query->where('users.company_id', companyId());
        }

        return $query;
    }

    public function byGuru()
    {
        $query = $this->select('users.*, roles.name AS role')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.role_id', '2')
            ->orderBy('users.id', 'DESC');
        if (!isSuperAdmin()) {
            $query->where('users.company_id', companyId());
        }

        return $query;
    }

    public function bySiswaHistory($filter = [])
    {
        $builder = $this->db->table('users u');

        // LAST HISTORY
        $builder->join(
            '(SELECT user_id, MAX(created_at) as last_date 
              FROM h_paket_approve 
              GROUP BY user_id) hlast',
            'hlast.user_id = u.id',
            'left'
        );

        $builder->join(
            'h_paket_approve h',
            'h.user_id = u.id AND h.created_at = hlast.last_date',
            'left'
        );

        // JOIN LAIN
        $builder->join('user_paket up', 'up.user_id = u.id', 'left');
        $builder->join('paket p', 'p.id = up.paket_id', 'left');

        // SELECT
        $builder->select('
            u.id,
            u.name,
            u.email,
            u.phone,

            up.program as user_program,
            p.nama as name_paket,

            COALESCE(up.status, up.status) as paket_status,
            COALESCE(h.expired_at, up.expired_at) as paket_exp,

            h.created_at as approved_at
        ');

        $builder->where('u.role_id', '3');

        // FILTER STATUS
        if (!empty($filter['status'])) {
            $builder->where('COALESCE(up.status, up.status)', $filter['status']);
        }

        // FILTER TANGGAL APPROVE
        if (!empty($filter['date_from'])) {
            $builder->where('DATE(h.created_at) >=', $filter['date_from']);
        }

        if (!empty($filter['date_to'])) {
            $builder->where('DATE(h.created_at) <=', $filter['date_to']);
        }

        $builder->orderBy('u.id', 'DESC');

        return $builder->get()->getResultArray();
    }
}
