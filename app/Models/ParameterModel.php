<?php

namespace App\Models;

use CodeIgniter\Model;

class ParameterModel extends Model
{
    protected $table = 'parameter';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'code',
        'value'
    ];

    public function getValue($code)
    {
        $row = $this->where('code', $code)->first();
        return json_decode($row['value'] ?? '[]', true);
    }
}
