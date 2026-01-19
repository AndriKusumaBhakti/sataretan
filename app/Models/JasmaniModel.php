<?php

namespace App\Models;

use CodeIgniter\Model;

class JasmaniModel extends Model
{
    protected $table = 'jasmani';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'kategori',
        'jenis_kelamin',
        'usia',
        'tinggi',
        'berat',
        'bmi',
        'kategori_bmi',
        'lari_12',
        'nilai_lari',
        'pull_up',
        'sit_up',
        'lunges',
        'push_up',
        'shuttle_run',
        'renang',
        'nilai_garjas_b',
        'nilai_total'
    ];

    protected $useTimestamps = true;

    public function getAllByUser()
    {

        $builder = $this->select('
            jasmani.*,
            users.name,
            users.email
        ')
            ->join('users', 'users.id = jasmani.user_id', 'left')
            ->orderBy('jasmani.created_at', 'DESC');

        // ✅ FILTER USER JIKA ADA
        if (user_id()) {
            $builder->where('jasmani.user_id', user_id());
        }

        return $builder->findAll();
    }
}
