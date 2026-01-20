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
        'tinggi_cm',
        'berat_kg',
        'bmi_index',
        'bmi_kategori',
        'lari_12',
        'nilai_lari_12',
        'pull_up',
        'nilai_pull_up',
        'sit_up',
        'nilai_sit_up',
        'lunges',
        'nilai_lunges',
        'push_up',
        'nilai_push_up',
        'shuttle_run',
        'nilai_shuttle_run',
        'renang',
        'nilai_renang',
        'nilai_garjas_b',
        'nilai_total',
        'created_at',
        'updated_at'
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
        if (!isGuruOrAdmin()) {
            $builder->where('jasmani.user_id', user_id());
        }

        return $builder->findAll();
    }
}
