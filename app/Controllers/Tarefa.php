<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

use App\Models\TarefaModel;
use App\Entities\Tarefa as TarefaEntity;

class Tarefa extends ResourceController
{
    use ResponseTrait;
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        
        $this->model = new TarefaModel();
        $this->fields = [
                "pessoa" => 'pessoa',
                "situacao"=> 'situacao',
                "prioridade"=> 'prioridade',
                "titulo"=> 'titulo',
                "descricao"=> 'descricao',
        ];
    }

    private function makeResponse($status,$message=null,$data=null,$error=false){
        $response = [
            'status'   => $status,
            'error'    => $error,
            'messages' => $message? [
                $error?'error':'success' => $message
            ] : [],
        ];

        $data ? $response['data'] = $data : null;

        switch($status){
            case 201:
                return $this->respondCreated($response);
                break;
            case 400:
                return $this->fail($message);
                break;
            case 404:
                return $this->failNotFound($message);
                break;
            default:
                return $this->respond($response,$status);
                break;
        }
    }

    private function getFieldsFromRequest(){
        $dataFromRequest = $this->request->getRawInput();
        $dataReturn = [];

        foreach($this->fields as $key=>$value){
            if(isset($dataFromRequest[$value])){
                $dataReturn[$key] = $dataFromRequest[$value];
            }
        }

        return $dataReturn;
    }

    // LIST ALL
    public function index()
     {
        try{
            $data['tarefas'] = $this->model->orderBy('tarefa.prioridade','ASC')->orderBy('tarefa.ido', 'ASC')->findAllJoinFilterColumns();  
            return $this->makeResponse(200,null,$data);
        }catch(\Exception $e){
            return $this->makeResponse(400,$e->getMessage(),null,true);
        }
     }
    
     // FIND ONE
    public function show($id=null)
    {
        if(is_null($id) || $id==''){
            return $this->makeResponse(400,'Você precisa enviar uma identificação de tarefa');
        }
        
        try{
            $data = $this->model->find($id);
            if($data){
                return $this->makeResponse(200,null,$data);
            }else{
                return $this->makeResponse(404,'Não encontramos a Tarefa',null, true);
            }
        }catch(\Exception $e){
            return $this->makeResponse(400,$e->getMessage(),null,true);
        }
    }

    // DELETE
    public function delete($id=null)
    {
        if(is_null($id) || $id==''){
            return $this->makeResponse(400,'Você precisa enviar uma identificação de tarefa');
        }

        try{
            $tarefa = $this->model->find($id);
            if($tarefa){
                $this->model->delete($id);
                return $this->makeResponse(200,'Tarefa removida com sucesso');
            }else{
                return $this->makeResponse(404,'Não encontramos a tarefa',null, true);
            }
        }catch(\Exception $e){
            return $this->makeResponse(400,$e->getMessage(),null,true);
        }
    }

    // CREATE
    public function create()
    {

        $dataFromRequest = $this->getFieldsFromRequest();

        if( 
            is_null($dataFromRequest['prioridade']) || $dataFromRequest['prioridade'] == '' ||
            is_null($dataFromRequest['titulo']) || $dataFromRequest['titulo'] == '' ||
            is_null($dataFromRequest['descricao']) || $dataFromRequest['descricao'] == ''
        ){
            return $this->makeResponse(400,'Ops, você esqueceu algum campo obrigatório',null,true);
        }

        if(array_key_exists('pessoa', $dataFromRequest) && !is_null($dataFromRequest['pessoa']) && $dataFromRequest['pessoa'] != ''){
            if($this->model->checkMore3Tarefas($dataFromRequest['pessoa'])){
                return $this->makeResponse(400,'Ops, você já atingiu o máximo de tarefas para este usuário',null,true);
            }
        }

        $dataFromRequest['situacao'] = 1;
        $dataFromRequest['pessoa'] = $dataFromRequest['pessoa'] == '0' ? null : $dataFromRequest['pessoa'];
        
        $tarefa = new TarefaEntity($dataFromRequest);
        try{
            $this->model->save($tarefa);
            return $this->makeResponse(201,'Tarefa adicionada com sucesso!');
        }catch(\Exception $e){
            return $this->makeResponse(400,$e->getMessage(),null,true);
        }
    }

    // UPDATE
    public function update($id=null)
    {
        if(is_null($id) || $id==''){
            return $this->makeResponse(400,'Você precisa enviar uma identificação de tarefa');
        }
        
        try{
            $objectUpdate = $this->model->find($id);

            if($objectUpdate)
            {
                $dataFromRequest = $this->getFieldsFromRequest();

                if(array_key_exists('pessoa', $dataFromRequest)){
                    if($objectUpdate->pessoa != $dataFromRequest['pessoa']){
                        if($dataFromRequest['pessoa'] == '0'){
                            $dataFromRequest['pessoa'] = null;
                        }else if($this->model->checkMore3Tarefas($dataFromRequest['pessoa'])){
                            return $this->makeResponse(400,'Ops, você já atingiu o máximo de tarefas para este usuário',null,true);
                        }
                        $dataFromRequest['ido_pessoa'] = $dataFromRequest['pessoa'];
                    }else{
                        unset($dataFromRequest['pessoa']);
                    }
                }

                if(isset($dataFromRequest['situacao'])){
                    $dataFromRequest['ido_situacao'] = $dataFromRequest['situacao'];
                    unset($dataFromRequest['situacao']);
                }

                foreach($dataFromRequest as $key=>$value){
                    $objectUpdate->$key = $value;
                }

                $this->model->save($objectUpdate);
                return $this->makeResponse(201,'Tarefa atualizada com sucesso!');
            }else{
                return $this->makeResponse(404,'Não encontramos a tarefa',null, true);
            }
        }catch(\Exception $e){
            return $this->makeResponse(400,$e->getMessage(),null,true);
        }
    }

}
