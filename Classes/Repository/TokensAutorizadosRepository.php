<?php


namespace Repository;

use DB\MySQL;


class TokensAutorizadosRepository
{
    private object $MySQL;

    public const TABELA = "tokens_auth";


    public function __construct()
    {
        $this->MySQL = new MySQL();
    }

    public function validarToken($token)
    {

        $token = str_replace([' ', 'Bearer'], '', $token); //Substituir espaços e a palavra Bearer por vazio de $token
        
        if($token){

        } else {

        }
        var_dump($token, 'TOKEN CHEGOU?');  // continuar o video a partir de 7:30
    }

    public function getMySQL()
    {
        return $this->MySQL;
    }
}
