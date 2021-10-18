<?php

namespace App\Models;

use CodeIgniter\Model;

class TarefaModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'tarefa';
    protected $primaryKey           = 'ido';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'App\Entities\Tarefa';
    protected $useSoftDeletes       = true;
    protected $protectFields        = true;
    protected $allowedFields        = [
        "ido_pessoa",
        "ido_situacao",
        "prioridade",
        "titulo",
        "descricao"
    ];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected function setCreatedAt(array $data) {
        $data['data']['created_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function setUpdatedAt(array $data) {
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = ['setCreatedAt'];
    protected $afterInsert          = [];
    protected $beforeUpdate         = ['setUpdatedAt'];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

}
