<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPaketModel extends Model
{
    protected $table = 'user_paket';
    protected $allowedFields = [
        'user_id',
        'program',
        'paket_id',
        'expired_at',
        'status'
    ];

    public function getActivePaket($userId)
    {
        return $this->select('paket.kode')
            ->join('paket', 'paket.id = user_paket.paket_id')
            ->where('user_id', $userId)
            ->where('paket.is_active', 1)
            ->groupStart()
            ->where('expired_at IS NULL')
            ->orWhere('expired_at >=', date('Y-m-d'))
            ->groupEnd()
            ->findAll();
    }

    public function getActivePaketUser($userId)
    {
        return $this->select('paket.kode')
            ->join('paket', 'paket.id = user_paket.paket_id')
            ->where('user_id', $userId)
            ->where('user_paket.status', 'A')
            ->where('paket.is_active', 1)
            ->groupStart()
            ->where('expired_at IS NULL')
            ->orWhere('expired_at >=', date('Y-m-d'))
            ->groupEnd()
            ->findAll();
    }

    public function getAllUserAktif()
    {
        return $this->select('users.*')
            ->join('users', 'users.id = user_paket.user_id')
            ->join('paket', 'paket.id = user_paket.paket_id')
            ->when(!isSuperAdmin(), function ($query) {
                $query->where('users.company_id', companyId());
            })
            ->where('user_paket.status', 'A')
            ->where('paket.is_active', 1)
            ->groupStart()
            ->where('user_paket.expired_at', null) // IS NULL
            ->orWhere('user_paket.expired_at >=', date('Y-m-d'))
            ->groupEnd()
            ->findAll();
    }
}
