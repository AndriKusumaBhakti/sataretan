<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table = 'company';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'address',
        'city',
        'phone',
        'status'
    ];
}
