<?php

namespace Util;

use InvalidArgumentException;
use JsonException;

class JsonUtil 
{

    public static function tratarCorpoRequisicaoJson(){

        try{
            $postJson = json_decode(file_get_contents('php://input'), true);


        }catch(JsonException $exception) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERR0_JSON_VAZIO);
        }


        if (is_array($postJson) && count($postJson) > 0 ){
            return $postJson;
        }
    }

}