<?php


namespace Repository;

use DB\MySQL;
use PDO;

class UsuariosRepository
{
    private object $MySQL;

    public const TABELA = "usuarios";


    public function __construct()
    {
        $this->MySQL = new MySQL();
    }

    public function insertUser($username, $password, $email, $nivel_auth)
    {
        $query = 'INSERT INTO ' . self::TABELA . '(username, password, email, nivel_auth) VALUES (:username, :password,:email, :nivel_auth)';

        $this->MySQL->getDb()->beginTransaction();
        $stmt = $this->MySQL->getDb()->prepare($query);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':nivel_auth', $nivel_auth);
        $stmt->execute();

        return $stmt->rowCount(); // retorna a quantidade de linhas afetadas , ou seja se afeto 1 linha é por que deu certo
    }

    public function updateUser($id, $dados)
    {
        $queryUpdate = 'UPDATE ' . self::TABELA . ' SET nome_completo = :nome_completo WHERE id = :id';

        $this->MySQL->getDb()->beginTransaction();
        $stmt = $this->MySQL->getDb()->prepare($queryUpdate);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome_completo', $dados['nome_completo']);
        $stmt->execute();

        return $stmt->rowCount(); // retorna a quantidade de linhas afetadas , ou seja se afeto 1 linha é por que deu certo
    }

    public function loginUser($email, $password)
    {
        $query = 'SELECT id, password FROM ' . self::TABELA . ' WHERE email = :email';
        $stmt = $this->MySQL->getDb()->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            // Verifica se a senha fornecida corresponde à senha do banco de dados
            if (password_verify($password, $user['password'])) {
                return ['id' => $user['id']]; // Retorna o Id
            } else {
                return ['erro' => "Usuário ou senha inválidos!"]; // Se a senha estiver incorreta, retorna 0
            }
        } else {
            return ['erro' => "Usuário ou senha inválidos!"]; // Retorna 0 se nenhum usuário for encontrado
        }
    }


    public function getMySQL()
    {
        return $this->MySQL;
    }
}
