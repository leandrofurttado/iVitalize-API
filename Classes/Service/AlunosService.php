<?php


namespace Service;

use Repository\AlunosRepository;
use Util\ConstantesGenericasUtil;

class AlunosService
{
    public const TABELA = 'alunos';
    public const RECURSOS_GET = ['listar', 'consultar'];
    public const RECURSOS_POST = ['matricular'];
    public const RECURSOS_ATUALIZAR = ['atualizar'];
    public const RECURSOS_LOGIN = ['login'];
    public const RECURSOS_BUSCARDADOS = ['buscardados', 'BuscarDados'];
    private array $dados;
    private array $dadosCorpoRequest;
    private object $AlunosRepository;

    /**
     * Summary of __construct
     * @param mixed $dados
     */
    public function __construct($dados = [])
    {
        $this->dados = $dados;

        $this->AlunosRepository = new AlunosRepository();
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
            $retorno = $this->CadastrarAluno(); // (CADASTRAR)
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
        return $this->AlunosRepository->getMySQL()->getItemPorId(self::TABELA, $this->dados['id']);
    }

    private function ListarTodosUsuarios()
    {
        return $this->AlunosRepository->getMySQL()->getAll(self::TABELA);
    }


    private function CadastrarAluno()
    {
        $nome_completo = $this->dadosCorpoRequest['nome_completo']; /* OBRIGATÓRIO */
        $primeiro_nome = $this->dadosCorpoRequest['primeiro_nome'];  /* OBRIGATÓRIO */
        $email_aluno = $this->dadosCorpoRequest['email_aluno']; 
        $cpf = $this->dadosCorpoRequest['cpf']; /* OBRIGATÓRIO */
        $data_nascimento = $this->dadosCorpoRequest['data_nascimento']; /* OBRIGATÓRIO */
        $sexo = $this->dadosCorpoRequest['sexo']; /* OBRIGATÓRIO */
        $telefone1 = $this->dadosCorpoRequest['telefone1']; 
        $telefone2 = $this->dadosCorpoRequest['telefone2'];
        $endereco = $this->dadosCorpoRequest['endereco']; /* OBRIGATÓRIO */
        $cep = $this->dadosCorpoRequest['cep']; /* OBRIGATÓRIO */
        $plano = $this->dadosCorpoRequest['plano'];  /* OBRIGATÓRIO  - CAMPO SELECT ENVIANDO O VALUE 1, 2, 3 */
        $horario_utilizacao = $this->dadosCorpoRequest['horario_utilizacao']; /* OBRIGATÓRIO */
        $valor_mensalidade = $this->dadosCorpoRequest['valor_mensalidade']; /* OBRIGATÓRIO */
        $descontos = $this->dadosCorpoRequest['descontos'];
        $data_pagamento = $this->dadosCorpoRequest['data_pagamento']; /* OBRIGATÓRIO DIA DE PAGAMENTO ENVIAR INT (CRIAR SELECT COM ALGUNS DIAS APENAS*/
        $modalidade = $this->dadosCorpoRequest['modalidade']; /* OBRIGATÓRIO  - CAMPO SELECT ENVIANDO O VALUE 1, 2, 3*/

        $data = array(
            'nome_completo' => $nome_completo,
            'primeiro_nome' => $primeiro_nome,
            'email_aluno' => $email_aluno,
            'cpf' => $cpf,
            'data_nascimento' => date('Y-m-d', strtotime($data_nascimento)),
            'sexo' => $sexo,
            'telefone1' => $telefone1,
            'telefone2' => $telefone2,
            'endereco' => $endereco,
            'cep' => $cep,
            'plano' => $plano,
            'horario_utilizacao' => $horario_utilizacao,
            'valor_mensalidade' => $valor_mensalidade,
            'descontos' => $descontos,
            'data_pagamento' => $data_pagamento,
            'modalidade' => $modalidade
        );

        if ($nome_completo && $primeiro_nome && $sexo && $valor_mensalidade && $plano && $data_nascimento && $horario_utilizacao) {
            if ($this->AlunosRepository->insertAluno($data)) {

                try {
                    $idCadastrado = $this->AlunosRepository->getMySQL()->getDb()->lastInsertId();
                    $this->AlunosRepository->getMySQL()->getDb()->commit();
                    return ['id_aluno' => $idCadastrado];
                } catch (\PDOException $e) {
                    throw new \InvalidArgumentException('Erro ao matricular aluno: ' . $e->getMessage());
                }
            }
        }

        throw new \InvalidArgumentException("Campos obrigatórios não foram preenchidos!"); //ERRO CASO NAO TIVER PREENCHIDO OS CAMPOS
    }

    private function LoginUsuario()
    {
        $password = $this->dadosCorpoRequest['password'];
        $email = $this->dadosCorpoRequest['email'];

        if ($email && $password) {
            return $this->AlunosRepository->loginUser($email, $password);
        }

        throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO); //ERRO CASO NAO TIVER PREENCHIDO OS CAMPOS
    }

    private function AtualizarUsuario()
    {
        if ($this->AlunosRepository->updateUser($this->dados['id'], $this->dadosCorpoRequest) > 0) {

            try {
                $this->AlunosRepository->getMySQL()->getDb()->commit();
                return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
            } catch (\PDOException $e) {
                throw new \InvalidArgumentException('Erro no banco de dados ao atualizar: ' . $e->getMessage());
            }
        }


        $this->AlunosRepository->getMySQL()->getDb()->rollback();
        throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }

    private function BuscarDadosUsuario()
    {
        $idUser = $this->dados['id'];

        if ($idUser) {
            return $this->AlunosRepository->buscarDadosUser($idUser);
        }

        throw new \InvalidArgumentException("ID é obrigatório, verifique novamente!"); //ERRO CASO NAO TIVER PREENCHIDO OS CAMPOS
    }
}
