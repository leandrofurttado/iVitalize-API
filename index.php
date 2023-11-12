<?php

use Util\ConstantesGenericasUtil;
use Util\RotasUtil;
use Validator\RequestValidator;

include 'bootstrap.php';



try{
    $RequestValidator = new RequestValidator(RotasUtil::getRotas());
    $retorno = $RequestValidator->processarRequest();
    
} catch (Exception $exception) {
    echo json_encode([
        ConstantesGenericasUtil::TIPO => ConstantesGenericasUtil::TIPO_ERRO,
        ConstantesGenericasUtil::RESPOSTA => $exception->getMessage()
    ]);
    exit;
}