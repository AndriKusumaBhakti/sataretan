<?php

namespace App\Models;

use CodeIgniter\Model;

class TryoutAttemptModel extends Model
{
    protected $table = 'tryout_attempts';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'tryout_id',
        'started_at',
        'finished_at',
        'skor_akhir',
        'status'
    ];
}
