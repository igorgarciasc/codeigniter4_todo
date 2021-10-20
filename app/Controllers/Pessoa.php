<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

use App\Models\PessoaModel;

class Pessoa extends ResourceController
{
    use ResponseTrait;
    public function __construct()
    {
        $this->model = new PessoaModel();
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

    //LIST ALL
    public function index()
    {
        try{
            $data['pessoas'] = $this->model->orderBy('ido', 'DESC')->findAll();    
            return $this->makeResponse(200,null,$data);
        }catch(\Exception $e){
            return $this->makeResponse(400,$e->getMessage(),null,true);
        }
    }

}
