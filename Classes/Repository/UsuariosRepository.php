<?php


namespace Repository;

use DB\MySQL;

class UsuariosRepository
{
    private object $MySQL;

    public const TABELA = "usuarios";


    public function __construct()
    {
        $this->MySQL = new MySQL();
    }

    public function insertUser($username, $password, $email, $nivel_auth) {
        $query = 'INSERT INTO ' . self::TABELA . '(username, password, email, nivel_auth) VALUES (:username, :password,:email, :nivel_auth)';

        $this->MySQL->getDb()->beginTransaction();
        $stmt = $this->MySQL->getDb()->prepare($query);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':nivel_auth', $nivel_auth);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function getMySQL()
    {
        return $this->MySQL;
    }
}
