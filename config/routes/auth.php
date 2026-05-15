<?php

// Rotas de autenticação e gerenciamento de perfil do usuário.
return [
    // Exibe formulário de login.
    'login' => [
        'type' => 'view',
        'target' => 'view/auth/login.php',
        'http_method' => 'GET',
    ],
    // Exibe formulário de cadastro.
    'cadastrar' => [
        'type' => 'view',
        'target' => 'view/auth/cadastrar.php',
        'http_method' => 'GET',
    ],
    // Processa autenticação do usuário.
    'processar_login' => [
        'type' => 'action',
        'controller_file' => 'controller/AuthController.php',
        'controller_class' => 'AuthController',
        'controller_method' => 'login',
        'http_method' => 'POST',
    ],
    // Processa criação de nova conta.
    'processar_cadastro' => [
        'type' => 'action',
        'controller_file' => 'controller/AuthController.php',
        'controller_class' => 'AuthController',
        'controller_method' => 'cadastrar',
        'http_method' => 'POST',
    ],
    // Exibe formulário de recuperação de senha (esqueci a senha)
    'recuperar_senha' => [
        'type' => 'view',
        'target' => 'view/auth/recuperar_senha.php',
        'http_method' => 'GET',
    ],
    // Processa envio/validação dos e-mails para permitir alteração de senha
    'processar_recuperar_senha' => [
        'type' => 'action',
        'controller_file' => 'controller/AuthController.php',
        'controller_class' => 'AuthController',
        'controller_method' => 'processarRecuperarSenha',
        'http_method' => 'POST',
    ],
    // Exibe formulário para alterar a senha (após verificação de e-mail)
    'alterar_senha' => [
        'type' => 'view',
        'target' => 'view/auth/alterar_senha.php',
        'http_method' => 'GET',
    ],
    // Processa a alteração de senha
    'processar_alterar_senha' => [
        'type' => 'action',
        'controller_file' => 'controller/AuthController.php',
        'controller_class' => 'AuthController',
        'controller_method' => 'processarAlterarSenha',
        'http_method' => 'POST',
    ],
    // Encerra a sessão do usuário autenticado.
    'sair' => [
        'type' => 'action',
        'controller_file' => 'controller/AuthController.php',
        'controller_class' => 'AuthController',
        'controller_method' => 'logout',
        'http_method' => 'POST',
    ],
    // Atualiza os dados do perfil do usuário.
    'processar_edicao_usuario' => [
        'type' => 'action',
        'controller_file' => 'controller/AuthController.php',
        'controller_class' => 'AuthController',
        'controller_method' => 'atualizarPerfil',
        'http_method' => 'POST',
    ],
    // Exclui a conta do usuário autenticado.
    'processar_exclusao_usuario' => [
        'type' => 'action',
        'controller_file' => 'controller/AuthController.php',
        'controller_class' => 'AuthController',
        'controller_method' => 'excluirConta',
        'http_method' => 'POST',
    ],
    // Exibe a página de perfil do usuário logado.
    'perfil_usuario' => [
        'type' => 'action',
        'controller_file' => 'controller/AuthController.php',
        'controller_class' => 'AuthController',
        'controller_method' => 'exibirPerfil',
        'http_method' => 'GET',
    ],
];
