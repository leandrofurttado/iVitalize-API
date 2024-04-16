<?php


namespace Service;

use Repository\UsuariosRepository;
use Util\ConstantesGenericasUtil;

class UsuariosService
{
    public const TABELA = 'usuarios';
    public const RECURSOS_GET = ['listar', 'consultar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_ATUALIZAR = ['atualizar'];
    public const RECURSOS_LOGIN = ['login'];
    public const RECURSOS_BUSCARDADOS = ['buscardados', 'BuscarDados'];
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


        if (in_array($recurso, self::RECURSOS_POST, true)) { // **** SE FOR CADASTRO ELE ENTRA AQUI
            $retorno = $this->CadastrarUsuario(); // (CADASTRAR)
        } elseif (in_array($recurso, self::RECURSOS_ATUALIZAR, true)) { // *** SE FOR UPDATE ELE ENTRA AQUI
            if ($this->dados['id'] > 0) {
                $retorno = $this->AtualizarUsuario();
            } else {
                throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
            }
        } elseif (in_array($recurso, self::RECURSOS_LOGIN, true)) {
            $retorno = $this->LoginUsuario();
        }
        elseif (in_array($recurso, self::RECURSOS_BUSCARDADOS, true)) {
            $retorno = $this->BuscarDadosUsuario();
        } else {
            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($retorno == null) {
            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
        }

        return $retorno;
    }

    private function buscarUsuarioId()
    {
        return $this->UsuariosRepository->getMySQL()->getItemPorId(self::TABELA, $this->dados['id']);
    }

    private function ListarTodosUsuarios()
    {
        return $this->UsuariosRepository->getMySQL()->getAll(self::TABELA);
    }


    private function CadastrarUsuario()
    {   
        $nome_completo = $this->dadosCorpoRequest['nome_completo'];
        $username = $this->dadosCorpoRequest['usuario'];
        $password = $this->dadosCorpoRequest['senha'];
        $email = $this->dadosCorpoRequest['email'];
        $sexo = $this->dadosCorpoRequest['sexo'];
        $data_nascimento = $this->dadosCorpoRequest['data_nascimento'];

        //hashando a senha:
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);


        if ($username && $password && $data_nascimento && $email) {
            if ($this->UsuariosRepository->insertUser($nome_completo, $username, $passwordHash, $email, $data_nascimento, $sexo)) {

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

    private function LoginUsuario()
    {
        $password = $this->dadosCorpoRequest['password'];
        $email = $this->dadosCorpoRequest['email'];

        if ($email && $password) {
            return $this->UsuariosRepository->loginUser($email, $password);
        }

        throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO); //ERRO CASO NAO TIVER PREENCHIDO OS CAMPOS
    }

    private function AtualizarUsuario()
    {
        if ($this->UsuariosRepository->updateUser($this->dados['id'], $this->dadosCorpoRequest) > 0) {

            try {
                $this->UsuariosRepository->getMySQL()->getDb()->commit();
                return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
            } catch (\PDOException $e) {
                throw new \InvalidArgumentException('Erro no banco de dados ao atualizar: ' . $e->getMessage());
            }
        }


        $this->UsuariosRepository->getMySQL()->getDb()->rollback();
        throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }

    private function BuscarDadosUsuario()
    {
        $idUser = $this->dados['id'];

        if ($idUser) {
            return $this->UsuariosRepository->buscarDadosUser($idUser);
        }

        throw new \InvalidArgumentException("ID é obrigatório, verifique novamente!"); //ERRO CASO NAO TIVER PREENCHIDO OS CAMPOS
    }
}
