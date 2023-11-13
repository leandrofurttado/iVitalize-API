<?php


namespace Service;
use Repository\UsuariosRepository;
use Util\ConstantesGenericasUtil;

class UsuariosService
{
    public const TABELA = 'usuarios';
    public const RECURSOS_GET = ['listar', 'consultar'];
    private array $dados;

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


    public function validarGet() {
        $retorno = null;

        $recurso = $this->dados['recurso'];

        if(in_array($recurso, self::RECURSOS_GET, true)){
            $retorno = $this->dados['id'] > 0 ? $this->buscarUsuarioId() : $this->ListarTodosUsuarios();
        } else {
            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($retorno == null){
            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
        }





        return $retorno;
    }

    public function buscarUsuarioId(){
        return $this->UsuariosRepository->getMySQL()->getItemPorId( self::TABELA, $this->dados['id']);
    }

    public function ListarTodosUsuarios(){
        return $this->UsuariosRepository->getMySQL()->getAll(self::TABELA);
    }

}
