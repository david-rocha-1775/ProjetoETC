<?php
// Controller de Autenticação (Login, Cadastro e Logout)

require_once "model/dao/UsuarioDAO.php";
require_once "model/dao/DenunciaDAO.php";
require_once "model/dao/CategoriaDAO.php";
require_once "model/dto/UsuarioDTO.php";

class AuthController
{
    private $usuarioDAO;
    private $denunciaDAO;
    private $categoriaDAO;
    private const MSG_CREDENCIAIS_INVALIDAS = 'Credenciais inválidas.';
    private const SENHA_MINIMA = 8;
    private const SENHA_MAXIMA = 72;

    public function __construct()
    {
        $this->usuarioDAO = new UsuarioDAO();
        $this->denunciaDAO = new DenunciaDAO();
        $this->categoriaDAO = new CategoriaDAO();
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
     * Valida a política única de senha do sistema.
     */
    private function validarPoliticaSenha($senha, $rotulo = 'A senha')
    {
        if (strlen($senha) < self::SENHA_MINIMA) {
            throw new Exception($rotulo . ' deve ter no mínimo 8 caracteres.');
        }

        if (strlen($senha) > self::SENHA_MAXIMA) {
            throw new Exception($rotulo . ' deve ter no máximo 72 caracteres.');
        }

        if (
            !preg_match('/[A-Z]/', $senha)
            || !preg_match('/[a-z]/', $senha)
            || !preg_match('/\d/', $senha)
            || !preg_match('/[^A-Za-z0-9]/', $senha)
        ) {
            throw new Exception($rotulo . ' deve conter ao menos uma letra maiúscula, uma letra minúscula, um número e um caractere especial.');
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
            $confirmacaoSenha = $_POST['confirmacao_senha'] ?? '';

            if ($nome === '' || $email === '' || $senha === '' || $confirmacaoSenha === '') {
                throw new Exception("Nome, e-mail, senha e confirmação de senha são obrigatórios.");
            }

            if (mb_strlen($nome) < 3 || mb_strlen($nome) > 100) {
                throw new Exception('O nome deve ter entre 3 e 100 caracteres.');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Informe um e-mail válido.");
            }

            $this->validarPoliticaSenha($senha, 'A senha');

            if ($senha !== $confirmacaoSenha) {
                throw new Exception("A confirmação de senha não confere.");
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
            $_SESSION['mensagem'] = $e->getMessage();
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

            if ($usuario === null || !password_verify($senha, $usuario->getSenha())) {
                $this->redirecionarComErro(self::MSG_CREDENCIAIS_INVALIDAS, 'login');
            }

            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $usuario->getId();
            $_SESSION['usuario_nome'] = $usuario->getNome();
            $_SESSION['usuario_email'] = $usuario->getEmail();
            $_SESSION['usuario_tipo'] = $usuario->getTipo();
            $_SESSION['logado'] = true;

            header("Location: index.php?rota=painel");
            exit();

        } catch (Exception $e) {
            $this->redirecionarComErro($e->getMessage(), 'login');
        }
    }

    /**
     * Exibe formulário para solicitar recuperação de senha (e-mail e confirmação).
     */
    public function exibirRecuperarSenha()
    {
        $tituloPagina = "Recuperar Senha";
        include "view/auth/recuperar_senha.php";
    }

    /**
     * Processa a solicitação de recuperação de senha.
     * Armazena em sessão o e-mail somente se existir no sistema.
     */
    public function processarRecuperarSenha()
    {
        $this->exigirMetodoPost('recuperar_senha');

        try {
            $email = trim($_POST['email'] ?? '');
            $confirmacao = trim($_POST['confirmacao_email'] ?? '');

            if ($email === '' || $confirmacao === '') {
                throw new Exception('Preencha o e-mail e sua confirmação.');
            }

            if ($email !== $confirmacao) {
                throw new Exception('Os e-mails informados não coincidem.');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Informe um e-mail válido.');
            }

            $usuario = $this->usuarioDAO->buscarPorEmail($email);

            // Por segurança, mensagem genérica; apenas quando existir armazenamos o e-mail em sessão
            if ($usuario !== null) {
                $_SESSION['recuperar_email'] = $email;
            }

            $_SESSION['mensagem'] = 'Se o e-mail estiver cadastrado, você poderá alterar a senha.';
            $_SESSION['tipo_mensagem'] = 'sucesso';
            header('Location: index.php?rota=alterar_senha');
            exit();

        } catch (Exception $e) {
            $this->redirecionarComErro($e->getMessage(), 'recuperar_senha');
        }
    }

    /**
     * Exibe formulário para alterar a senha. Requer que `recuperar_email` esteja em sessão.
     */
    public function exibirAlterarSenha()
    {
        if (empty($_SESSION['recuperar_email'])) {
            $this->redirecionarComErro('Acesse o processo de recuperação primeiro.', 'recuperar_senha');
        }

        $tituloPagina = "Alterar Senha";
        include "view/auth/alterar_senha.php";
    }

    /**
     * Processa a alteração de senha após verificação do e-mail.
     */
    public function processarAlterarSenha()
    {
        $this->exigirMetodoPost('alterar_senha');

        try {
            if (empty($_SESSION['recuperar_email'])) {
                throw new Exception('Fluxo de recuperação inválido.');
            }

            $email = $_SESSION['recuperar_email'];
            $nova = $_POST['nova_senha'] ?? '';
            $conf = $_POST['confirmacao_senha'] ?? '';

            if ($nova === '' || $conf === '') {
                throw new Exception('Preencha a nova senha e a confirmação.');
            }

            if ($nova !== $conf) {
                throw new Exception('A confirmação da senha não confere.');
            }

            $this->validarPoliticaSenha($nova, 'A senha');

            $hash = password_hash($nova, PASSWORD_DEFAULT);

            $atualizou = $this->usuarioDAO->atualizarSenhaPorEmail($email, $hash);
            if (!$atualizou) {
                throw new Exception('Não foi possível alterar a senha neste momento.');
            }

            // Limpa sinalização de recuperação
            unset($_SESSION['recuperar_email']);

            $_SESSION['mensagem'] = 'Senha alterada com sucesso. Faça login com a nova senha.';
            $_SESSION['tipo_mensagem'] = 'sucesso';

            header('Location: index.php?rota=login');
            exit();

        } catch (Exception $e) {
            $this->redirecionarComErro($e->getMessage(), 'alterar_senha');
        }
    }

    /**
     * Destrói a sessão atual.
     */
    public function logout()
    {
        $this->exigirMetodoPost('painel');
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
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

            $paginaDenuncias = (int) ($_GET['minhas_pagina'] ?? 1);
            if ($paginaDenuncias < 1) {
                $paginaDenuncias = 1;
            }

            $limiteDenuncias = 5;
            $totalMinhasDenuncias = $this->denunciaDAO->contarPaginadasPorUsuario($idUsuario);
            $totalPaginasMinhasDenuncias = $totalMinhasDenuncias > 0
                ? (int) ceil($totalMinhasDenuncias / $limiteDenuncias)
                : 0;

            if ($totalPaginasMinhasDenuncias > 0 && $paginaDenuncias > $totalPaginasMinhasDenuncias) {
                $paginaDenuncias = $totalPaginasMinhasDenuncias;
            }

            $minhasDenuncias = $this->denunciaDAO->listarPaginadasPorUsuario(
                $idUsuario,
                $paginaDenuncias,
                $limiteDenuncias,
                'recentes'
            );

            $totaisMinhasDenunciasStatus = $this->denunciaDAO->contarTotaisPorStatusPorUsuario($idUsuario);
            $categorias = $this->categoriaDAO->listarTodas();
            $categoriasPorId = [];

            foreach ($categorias as $categoria) {
                $categoriasPorId[(int) $categoria->getId()] = (string) $categoria->getNomeCategoria();
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
        $this->exigirMetodoPost('perfil_usuario');

        try {
            $idUsuario = (int) $_SESSION['usuario_id'];
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');

            if ($nome === '' || $email === '') {
                throw new Exception("Nome e e-mail são obrigatórios.");
            }

            if (mb_strlen($nome) < 3 || mb_strlen($nome) > 100) {
                throw new Exception('O nome deve ter entre 3 e 100 caracteres.');
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

                $this->validarPoliticaSenha($novaSenha, 'A nova senha');

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
            header("Location: index.php?rota=perfil_usuario");
            exit();

        } catch (Exception $e) {
            $this->redirecionarComErro($e->getMessage(), 'perfil_usuario');
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

            $excluiu = $this->usuarioDAO->excluirPorId($idUsuario);
            if (!$excluiu) {
                throw new Exception("Não foi possível excluir a conta neste momento.");
            }

            session_unset();
            session_destroy();
            session_start();
            session_regenerate_id(true);
            $_SESSION['mensagem'] = "Conta excluída com sucesso.";
            $_SESSION['tipo_mensagem'] = "sucesso";
            header("Location: index.php?rota=login");
            exit();

        } catch (Exception $e) {
            $this->redirecionarComErro($e->getMessage(), 'painel');
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