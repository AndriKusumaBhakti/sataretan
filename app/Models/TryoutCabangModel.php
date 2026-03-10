<?php

namespace App\Models;

use CodeIgniter\Model;

class TryoutCabangModel extends Model
{
    protected $table = 'tryout_cabang';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'company_id',
        'category',
        'key',
        'value',
        'mode',
        'persen',
        'penilaian_type'
    ];

    protected $useTimestamps = false;
}