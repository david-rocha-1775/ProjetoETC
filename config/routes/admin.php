<?php

// Rotas administrativas (acesso restrito a usuários do tipo admin).
return [
    // Lista usuários cadastrados no sistema.
    'listar_usuarios' => [
        'type' => 'action',
        'controller_file' => 'controller/AdminController.php',
        'controller_class' => 'AdminController',
        'controller_method' => 'listarUsuarios',
        'http_method' => 'GET',
    ],
    // Processa cadastro de nova categoria.
    'processar_cadastro_categoria' => [
        'type' => 'action',
        'controller_file' => 'controller/AdminController.php',
        'controller_class' => 'AdminController',
        'controller_method' => 'cadastrarCategoria',
        'http_method' => 'POST',
    ],
    // Processa edição de categoria existente.
    'processar_edicao_categoria' => [
        'type' => 'action',
        'controller_file' => 'controller/AdminController.php',
        'controller_class' => 'AdminController',
        'controller_method' => 'atualizarCategoria',
        'http_method' => 'POST',
    ],
    // Processa exclusão de categoria existente.
    'processar_exclusao_categoria' => [
        'type' => 'action',
        'controller_file' => 'controller/AdminController.php',
        'controller_class' => 'AdminController',
        'controller_method' => 'excluirCategoria',
        'http_method' => 'POST',
    ],
];
