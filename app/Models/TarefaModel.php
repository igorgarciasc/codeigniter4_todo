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
    protected $beforeUpdate         = ['setUpdatedAt'];

    public function findAllJoinFilterColumns() {
        $tarefas = $this
            ->select(['tarefa.ido','tarefa.prioridade','tarefa.titulo','tarefa.descricao','tarefa.ido_pessoa','pessoa.nome as pessoa_nome','tarefa.ido_situacao','situacao.descricao as situacao_descricao'])
            ->join('pessoa', 'tarefa.ido_pessoa = pessoa.ido', 'left')
            ->join('situacao_tarefa as situacao','tarefa.ido_situacao = situacao.ido')
            ->findAll();
        return $tarefas;
    }

    public function checkMore3Tarefas($idoPessoa) {
        
        $db      = \Config\Database::connect();
        $builder = $db->table('tarefa');
        $builder->where('ido_pessoa',$idoPessoa);
        $builder->where('deleted_at is null');
        $totalTarefas = $builder->countAllResults();

        if($totalTarefas >= 3){
            return true;
        }else{
            return false;
        }
    }

}