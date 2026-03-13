<?php
// Controller de Autenticação (Login, Cadastro e Logout)

require_once "model/dao/UsuarioDAO.php";
require_once "model/dto/UsuarioDTO.php";

class AuthController
{
    private $usuarioDAO;

    public function __construct()
    {
        $this->usuarioDAO = new UsuarioDAO();
    }

    /**
     * Processa o formulário de cadastro de um novo usuário.
     */
    public function cadastrar()
    {
        try {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $usuario = new UsuarioDTO();
            $usuario->setNome($nome);
            $usuario->setEmail($email);
            $usuario->setSenha(password_hash($senha, PASSWORD_DEFAULT));

            $this->usuarioDAO->cadastrar($usuario);

            $_SESSION['mensagem'] = "Usuário cadastrado com sucesso!";
            $_SESSION['tipo_mensagem'] = "sucesso";
            header("Location: index.php?rota=login");
            exit();

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro ao cadastrar: " . $e->getMessage();
            $_SESSION['tipo_mensagem'] = "erro";
            header("Location: index.php?rota=cadastrar");
            exit();
        }
    }

    /**
     * Valida as credenciais e inicia a sessão do usuário.
     */
    public function login()
    {
        try {
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $usuario = $this->usuarioDAO->buscarPorEmail($email);

            if ($usuario !== null) {
                if (password_verify($senha, $usuario->getSenha())) {
                    $_SESSION['usuario_id'] = $usuario->getId();
                    $_SESSION['usuario_nome'] = $usuario->getNome();
                    $_SESSION['logado'] = true;

                    header("Location: index.php?rota=painel");
                    exit();
                } else {
                    $this->redirecionarComErro("Senha incorreta.", "login");
                }
            } else {
                $this->redirecionarComErro("Usuário não encontrado.", "login");
            }

        } catch (Exception $e) {
            $this->redirecionarComErro("Erro ao processar login: " . $e->getMessage(), "login");
        }
    }

    /**
     * Destrói a sessão atual.
     */
    public function logout()
    {
        session_destroy();
        header("Location: index.php?rota=login");
        exit();
    }

    /**
     * Helper para redirecionamento com mensagens de erro.
     */
    private function redirecionarComErro($mensagem, $rota)
    {
        $_SESSION['mensagem'] = $mensagem;
        $_SESSION['tipo_mensagem'] = "erro";
        header("Location: index.php?rota=" . $rota);
        exit();
    }
}
?>