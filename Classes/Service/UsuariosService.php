<?php


namespace Service;

use Repository\UsuariosRepository;
use Util\ConstantesGenericasUtil;

class UsuariosService
{
    public const TABELA = 'usuarios';
    public const RECURSOS_GET = ['listar', 'consultar'];
    public const RECURSOS_POST = ['cadastrar'];
    private array $dados;
    private array $dadosCorpoRequest;
    private object $UsuariosRepository;

    /**
     * Summary of __construct
     * @param mixed $dados
     */
    public function __construct($dados = [])
    {
        $this->dados = $dados;

        $this->UsuariosRepository = new UsuariosRepository();
    }

    public function tratarDadosRequestPost($dadosRequest)
    {
        $this->dadosCorpoRequest = $dadosRequest;
    }


    public function validarGet()
    {
        $retorno = null;

        $recurso = $this->dados['recurso'];

        if (in_array($recurso, self::RECURSOS_GET, true)) {
            $retorno = $this->dados['id'] > 0 ? $this->buscarUsuarioId() : $this->ListarTodosUsuarios();
        } else {
            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($retorno == null) {
            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
        }

        return $retorno;
    }

    public function validarPost()
    {
        $retorno = null;

        $recurso = $this->dados['recurso'];


        if (in_array($recurso, self::RECURSOS_POST, true)) {
            $retorno = $this->CadastrarUsuario(); // (CADASTRAR)
        } else {
            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($retorno == null) {
            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
        }

        return $retorno;
    }

    public function buscarUsuarioId()
    {
        return $this->UsuariosRepository->getMySQL()->getItemPorId(self::TABELA, $this->dados['id']);
    }

    public function ListarTodosUsuarios()
    {
        return $this->UsuariosRepository->getMySQL()->getAll(self::TABELA);
    }


    public function CadastrarUsuario()
    {
        $username = $this->dadosCorpoRequest['username'];
        $password = $this->dadosCorpoRequest['password'];
        $email = $this->dadosCorpoRequest['email'];
        $nivel_auth = $this->dadosCorpoRequest['nivel_auth'];


        if ($username && $password && $nivel_auth && $email) {
            if ($this->UsuariosRepository->insertUser($username, $password, $email, $nivel_auth)) {   

                try {
                    $idCadastrado = $this->UsuariosRepository->getMySQL()->getDb()->lastInsertId();
                    $this->UsuariosRepository->getMySQL()->getDb()->commit();
                    return ['id_cadastrado' => $idCadastrado];
                } catch (\PDOException $e) {
                    throw new \InvalidArgumentException('Erro no banco de dados ao cadastrar: ' . $e->getMessage());
                }     
            }
        }

        throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO); //ERRO CASO NAO TIVER PREENCHIDO OS CAMPOS
    }
}
