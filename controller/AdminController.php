<?php
// Controller Administrativo (Ações restritas a administradores)

require_once "model/dao/UsuarioDAO.php";

class AdminController
{
    private $usuarioDAO;

    public function __construct()
    {
        $this->usuarioDAO = new UsuarioDAO();
    }

    /**
     * Lista usuários cadastrados (uso administrativo).
     */
    public function listarUsuarios()
    {
        $this->exigirLogin();
        $this->exigirAdministrador();

        try {
            $usuarios = $this->usuarioDAO->listarUsuarios();
            $resposta = [];

            foreach ($usuarios as $usuario) {
                $resposta[] = [
                    'id_usuario' => $usuario->getId(),
                    'nome' => $usuario->getNome(),
                    'email' => $usuario->getEmail(),
                    'tipo' => $usuario->getTipo(),
                    'data_cadastro' => $usuario->getDataCadastro(),
                ];
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($resposta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit();

        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode([
                'erro' => 'Erro ao listar usuarios.',
                'detalhe' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit();
        }
    }

    /**
     * Garante que a sessão esteja autenticada.
     */
    private function exigirLogin()
    {
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['usuario_id'])) {
            $this->redirecionarComErro('Faça login para continuar.', 'login');
        }
    }

    /**
     * Garante que o usuário autenticado seja administrador.
     */
    private function exigirAdministrador()
    {
        if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
            $this->redirecionarComErro('Acesso restrito a administradores.', 'painel');
        }
    }

    /**
     * Helper para redirecionamento com mensagens de erro.
     */
    private function redirecionarComErro($mensagem, $rota)
    {
        $_SESSION['mensagem'] = $mensagem;
        $_SESSION['tipo_mensagem'] = 'erro';
        header('Location: index.php?rota=' . $rota);
        exit();
    }
}
?>