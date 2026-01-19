<?php

namespace App\Models;
use CodeIgniter\Model;


class PaketApproveHistory extends Model
{
    protected $table = 'h_paket_approve';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'paket_id',
        'approved_by',
        'approved_at',
        'expired_at',
        'note',
    ];

    protected $useTimestamps = false;
}
