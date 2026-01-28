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
        'nilai_lari_12',

        'pull_up',
        'chinning',
        'sit_up',
        'push_up',
        'shuttle_run',
        'renang',

        'nilai_pull_up',
        'nilai_chinning',
        'nilai_sit_up',
        'nilai_push_up',
        'nilai_shuttle_run',
        'nilai_renang',

        'nilai_garjas_b'
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
            ->when(!isSuperAdmin(), function ($query) {
                $query->where('company_id', companyId());
            })
            ->orderBy('jasmani.created_at', 'DESC');

        // ✅ FILTER USER JIKA ADA
        if (!isGuruOrAdmin()) {
            $builder->where('jasmani.user_id', user_id());
        }

        return $builder->findAll();
    }
}
