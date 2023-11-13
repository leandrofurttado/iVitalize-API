<?php


namespace Repository;

use DB\MySQL;
use InvalidArgumentException;
use Util\ConstantesGenericasUtil;

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
            $consultaTokenExiste = 'SELECT id FROM ' . self::TABELA . ' WHERE token = :token AND status = :status';
            $stmt = $this->getMySQL()->getDb()->prepare($consultaTokenExiste);
            $stmt->bindValue(':token', $token);
            $stmt->bindValue(':status', 'S'); //SOMENTE OS ATIVOS (S)
            $stmt->execute();

            if($stmt->rowCount() !== 1){
                header(http_response_code(401));
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TOKEN_NAO_AUTORIZADO);
            }

        } else {
            throw new  \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TOKEN_NAO_AUTORIZADO);
        }
    }

    public function getMySQL()
    {
        return $this->MySQL;
    }
}
