<?php

namespace App\Models;

use CodeIgniter\Model;

class TryoutJawabanModel extends Model
{
    protected $table = 'tryout_jawaban_user';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'tryout_id',
        'soal_id',
        'jawaban',
        'updated_at'
    ];
}
