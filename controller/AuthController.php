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
     * Garante que a requisição seja POST antes de processar a ação.
     */
    private function exigirMetodoPost($rotaRetorno)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionarComErro("Método inválido.", $rotaRetorno);
        }
    }

    /**
     * Processa o formulário de cadastro de um novo usuário.
     */
    public function cadastrar()
    {
        $this->exigirMetodoPost('cadastrar');

        try {
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            if ($nome === '' || $email === '' || $senha === '') {
                throw new Exception("Nome, e-mail e senha são obrigatórios.");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Informe um e-mail válido.");
            }

            if (strlen($senha) < 6) {
                throw new Exception("A senha deve ter no mínimo 6 caracteres.");
            }

            if ($this->usuarioDAO->emailJaCadastrado($email)) {
                throw new Exception("Este e-mail já está cadastrado.");
            }

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
        $this->exigirMetodoPost('login');

        try {
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            if ($email === '' || $senha === '') {
                throw new Exception("Informe e-mail e senha.");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Informe um e-mail válido.");
            }

            $usuario = $this->usuarioDAO->buscarPorEmail($email);

            if ($usuario !== null) {
                if (password_verify($senha, $usuario->getSenha())) {
                    $_SESSION['usuario_id'] = $usuario->getId();
                    $_SESSION['usuario_nome'] = $usuario->getNome();
                    $_SESSION['usuario_email'] = $usuario->getEmail();
                    $_SESSION['usuario_tipo'] = $usuario->getTipo();
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
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['mensagem'] = 'Você saiu da conta com sucesso.';
        $_SESSION['tipo_mensagem'] = 'sucesso';
        header("Location: index.php?rota=login");
        exit();
    }

    /**
     * Exibe a tela de perfil do usuário logado.
     */
    public function exibirPerfil()
    {
        $this->exigirLogin();

        try {
            $idUsuario = (int) $_SESSION['usuario_id'];
            $usuario = $this->usuarioDAO->buscarPorId($idUsuario);

            if ($usuario === null) {
                throw new Exception("Usuário não encontrado.");
            }

            $tituloPagina = "Meu Perfil";
            include "view/auth/perfil.php";

        } catch (Exception $e) {
            $this->redirecionarComErro("Erro ao carregar perfil: " . $e->getMessage(), "painel");
        }
    }

    /**
     * Atualiza os dados do usuário logado (nome, e-mail e senha opcional).
     */
    public function atualizarPerfil()
    {
        $this->exigirLogin();
        $this->exigirMetodoPost('painel');

        try {
            $idUsuario = (int) $_SESSION['usuario_id'];
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');

            if ($nome === '' || $email === '') {
                throw new Exception("Nome e e-mail são obrigatórios.");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Informe um e-mail válido.");
            }

            $usuarioAtual = $this->usuarioDAO->buscarPorId($idUsuario);
            if ($usuarioAtual === null) {
                throw new Exception("Usuário não encontrado para atualização.");
            }

            if ($this->usuarioDAO->emailJaCadastrado($email, $idUsuario)) {
                throw new Exception("Este e-mail já está em uso por outro usuário.");
            }

            $senhaAtual = $_POST['senha_atual'] ?? '';
            $novaSenha = $_POST['nova_senha'] ?? '';
            $confirmacaoSenha = $_POST['confirmacao_senha'] ?? '';
            $senhaFinal = $usuarioAtual->getSenha();

            $alterarSenha = ($senhaAtual !== '' || $novaSenha !== '' || $confirmacaoSenha !== '');
            if ($alterarSenha) {
                if ($senhaAtual === '' || $novaSenha === '' || $confirmacaoSenha === '') {
                    throw new Exception("Para trocar a senha, preencha senha atual, nova senha e confirmação.");
                }

                if (!password_verify($senhaAtual, $usuarioAtual->getSenha())) {
                    throw new Exception("Senha atual incorreta.");
                }

                if ($novaSenha !== $confirmacaoSenha) {
                    throw new Exception("A confirmação da nova senha não confere.");
                }

                if (strlen($novaSenha) < 6) {
                    throw new Exception("A nova senha deve ter no mínimo 6 caracteres.");
                }

                $senhaFinal = password_hash($novaSenha, PASSWORD_DEFAULT);
            }

            $usuario = new UsuarioDTO();
            $usuario->setId($idUsuario);
            $usuario->setNome($nome);
            $usuario->setEmail($email);
            $usuario->setSenha($senhaFinal);

            $this->usuarioDAO->atualizar($usuario);

            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_email'] = $email;
            $_SESSION['mensagem'] = "Dados atualizados com sucesso!";
            $_SESSION['tipo_mensagem'] = "sucesso";
            header("Location: index.php?rota=painel");
            exit();

        } catch (Exception $e) {
            $this->redirecionarComErro("Erro ao atualizar perfil: " . $e->getMessage(), "painel");
        }
    }

    /**
     * Exclui a conta do usuário logado.
     */
    public function excluirConta()
    {
        $this->exigirLogin();
        $this->exigirMetodoPost('painel');

        try {
            $idUsuario = (int) $_SESSION['usuario_id'];
            $senhaConfirmacao = $_POST['senha_confirmacao'] ?? '';

            if ($senhaConfirmacao === '') {
                throw new Exception("Informe sua senha para confirmar a exclusão da conta.");
            }

            $usuario = $this->usuarioDAO->buscarPorId($idUsuario);
            if ($usuario === null) {
                throw new Exception("Usuário não encontrado para exclusão.");
            }

            if (!password_verify($senhaConfirmacao, $usuario->getSenha())) {
                throw new Exception("Senha de confirmação inválida.");
            }

            $this->usuarioDAO->excluirPorId($idUsuario);

            session_unset();
            session_destroy();
            session_start();
            $_SESSION['mensagem'] = "Conta excluída com sucesso.";
            $_SESSION['tipo_mensagem'] = "sucesso";
            header("Location: index.php?rota=login");
            exit();

        } catch (Exception $e) {
            $this->redirecionarComErro("Erro ao excluir conta: " . $e->getMessage(), "painel");
        }
    }

    /**
     * Garante que a sessão esteja autenticada.
     */
    private function exigirLogin()
    {
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['usuario_id'])) {
            $this->redirecionarComErro("Faça login para continuar.", "login");
        }
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