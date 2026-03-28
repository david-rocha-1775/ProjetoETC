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
    // Encerra a sessão do usuário autenticado.
    'sair' => [
        'type' => 'action',
        'controller_file' => 'controller/AuthController.php',
        'controller_class' => 'AuthController',
        'controller_method' => 'logout',
        'http_method' => 'GET',
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
