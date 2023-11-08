<?php

namespace Validator;

use Repository\TokensAutorizadosRepository;
use Util\ConstantesGenericasUtil;
use Util\JsonUtil;

class RequestValidator
{
    
    private string $request;
    private array $dadosRequest;
    private object $TokensAutorizadosRepository;

    public function __construct($request)
    {
        $this->request = $request;
        $this->TokensAutorizadosRepository = new TokensAutorizadosRepository();

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

        $this->TokensAutorizadosRepository->validarToken(getallheaders()['Authorization']);

    }
}
