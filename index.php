<?php
// Arquivo de Roteamento (Front Controller)
// Ponto de entrada do sistema que direciona a requisição para o Controller específico

session_start();

$rota = isset($_GET['rota']) ? $_GET['rota'] : 'inicio';

switch ($rota) {

    // --- Rotas Públicas ---
    case 'inicio':
        include 'view/public/home.php';
        break;

    case 'testar_conexao':
        require 'controller/TestarConexao.php';
        break;

    // --- Autenticação ---
    case 'login':
        include 'view/auth/login.php';
        break;

    case 'processar_login':
        require_once 'controller/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;

    case 'cadastrar':
        include 'view/auth/cadastrar.php';
        break;

    case 'processar_cadastro':
        require_once 'controller/AuthController.php';
        $controller = new AuthController();
        $controller->cadastrar();
        break;

    case 'sair':
        require_once 'controller/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'processar_edicao_usuario':
        require_once 'controller/AuthController.php';
        $controller = new AuthController();
        $controller->atualizarPerfil();
        break;

    case 'processar_exclusao_usuario':
        require_once 'controller/AuthController.php';
        $controller = new AuthController();
        $controller->excluirConta();
        break;

    case 'listar_usuarios':
        require_once 'controller/AdminController.php';
        $controller = new AdminController();
        $controller->listarUsuarios();
        break;

    // --- Rotas Protegidas ---
    case 'painel':
        require_once 'controller/PainelController.php';
        $controller = new PainelController();
        $controller->index();
        break;

    case 'nova_denuncia':
        require_once 'controller/PainelController.php';
        $controller = new PainelController();
        $controller->cadastrarDenuncia();
        break;

    // --- Not Found ---
    default:
        echo "<h1>Erro 404 - Página não encontrada</h1>";
        echo "<a href='index.php?rota=inicio'>Voltar ao Início</a>";
        break;
}
?>