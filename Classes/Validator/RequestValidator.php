<?php

namespace Validator;

use Repository\TokensAutorizadosRepository;
use Service\UsuariosService;
use Util\ConstantesGenericasUtil;
use Util\JsonUtil;

class RequestValidator
{
    
    private $request;
    private array $dadosRequest = [];
    private object $TokensAutorizadosRepository;

    
    public function __construct($request = [])
    {
        $this->TokensAutorizadosRepository = new TokensAutorizadosRepository();
        $this->request = $request;
    }



    public function processarRequest()//irá separar as requests, detalhando e enviando para o seu lugar o que é post, o que é delete, o que é get
    { 
        $retorno = mb_convert_encoding(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA, 'UTF-8', 'ISO-8859-1');

        if (in_array($this->request['metodo'], ConstantesGenericasUtil::TIPO_REQUEST, true)){
            $retorno = $this->direcionarRequest();
        }

        return $retorno;

    }


    private function direcionarRequest() //Agora aqui vai direcionar cada METODO para o seu lugar correto (PUT, POST, GET, DELETE)
    {   

        if ($this->request['metodo'] == 'POST'){
            $this->dadosRequest = JsonUtil::tratarCorpoRequisicaoJson();
        }

        if ($this->request['metodo'] == 'PUT'){
            $this->dadosRequest = JsonUtil::tratarCorpoRequisicaoJson();
        }

        $this->TokensAutorizadosRepository->validarToken(getallheaders()['authorization']);

        $metodo = $this->request['metodo'];


        return $this->$metodo(); //aqui é o mesmo que $this->get(); ou $this->post(); pq ele pega o request e transforma em function.

    }
    
    private function get()
    {   
        $retorno = mb_convert_encoding(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA, 'UTF-8', 'ISO-8859-1');


        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_GET, true)) {
            switch ($this->request['rota']) {
                case 'USUARIOS': //ROTA /usuarios
                    $UsuariosService = new UsuariosService($this->request);
                    $retorno = $UsuariosService->validarGet();
                    break;
                default:
                    throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }
        return $retorno;
    }

    private function post()
    {   
        $retorno = mb_convert_encoding(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA, 'UTF-8', 'ISO-8859-1');


        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_POST, true)) {
            switch ($this->request['rota']) {
                case 'USUARIOS': //ROTA /usuarios
                    $UsuariosService = new UsuariosService($this->request);
                    $UsuariosService->tratarDadosRequestPost($this->dadosRequest);
                    $retorno = $UsuariosService->validarPost();
                    break;
                default:
                    throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }
        return $retorno;
    }

}
