<?php
// A sessão agora é iniciada aqui para TODO o sistema!
session_start();

// Pega a rota da URL. Se a URL estiver vazia, o padrão é 'inicio'
$rota = isset($_GET['rota']) ? $_GET['rota'] : 'inicio';

// Decide qual arquivo carregar com base na rota
switch ($rota) {
    case 'inicio':
        include 'view/HomeView.php';
        break;
    case 'testar_conexao':
        require 'controller/TestarConexao.php';
        break;
    case 'login':
        include 'view/LoginUsuarioView.php';
        break;
    case 'processar_login':
        require 'controller/LoginUsuarioController.php';
        break;
    case 'cadastrar':
        include 'view/CadastrarUsuarioView.php';
        break;
    case 'processar_cadastro':
        require 'controller/CadastrarUsuarioController.php';
        break;
    case 'painel':
        require 'controller/PainelUsuarioController.php';
        break;
    case 'sair':
        session_destroy();
        header("Location: index.php?rota=inicio");
        break;
    case 'nova_denuncia':
        include 'view/NovaDenunciaView.php';  
        break;
     case 'processar_denuncia':
        require 'controller/ProcessarDenunciaController.php';
        break;   
    default:
        echo "<h1>Erro 404 - Página não encontrada</h1>";
        echo "<a href='index.php?rota=inicio'>Voltar ao Início</a>";
        break;
}
?>