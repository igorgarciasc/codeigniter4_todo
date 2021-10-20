<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Tarefa extends Entity
{
    protected $datamap = [
        "pessoa"=>"ido_pessoa",
        "situacao"=>"ido_situacao"
    ];

    protected $dates   = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts   = [];

}
