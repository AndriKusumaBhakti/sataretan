<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriDetailModel extends Model
{
    protected $table = 'materi_detail';
    protected $allowedFields = [
        'materi_id','sub_judul','file','link','urutan'
    ];
}
