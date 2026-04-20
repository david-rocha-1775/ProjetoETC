<?php

// Rotas administrativas (acesso a admin e gestor, com ações específicas ainda restritas ao admin).
return [
    // Exibe o dashboard administrativo com métricas básicas.
    'admin_dashboard' => [
        'type' => 'action',
        'controller_file' => 'controller/AdminController.php',
        'controller_class' => 'AdminController',
        'controller_method' => 'dashboard',
        'http_method' => 'GET',
    ],
    // Lista usuários cadastrados no sistema.
    'listar_usuarios' => [
        'type' => 'action',
        'controller_file' => 'controller/AdminController.php',
        'controller_class' => 'AdminController',
        'controller_method' => 'listarUsuarios',
        'http_method' => 'GET',
    ],
    // Lista categorias para gerenciamento administrativo.
    'listar_categorias_admin' => [
        'type' => 'action',
        'controller_file' => 'controller/AdminController.php',
        'controller_class' => 'AdminController',
        'controller_method' => 'listarCategorias',
        'http_method' => 'GET',
    ],
    // Lista denúncias para moderação administrativa.
    'admin_denuncias' => [
        'type' => 'action',
        'controller_file' => 'controller/AdminController.php',
        'controller_class' => 'AdminController',
        'controller_method' => 'listarDenuncias',
        'http_method' => 'GET',
    ],
    // Processa alteração de status de denúncia por admin.
    'processar_status_denuncia_admin' => [
        'type' => 'action',
        'controller_file' => 'controller/AdminController.php',
        'controller_class' => 'AdminController',
        'controller_method' => 'atualizarStatusDenuncia',
        'http_method' => 'POST',
    ],
    // Processa alternância de ativo de denúncia por admin.
    'processar_alternancia_ativo_denuncia_admin' => [
        'type' => 'action',
        'controller_file' => 'controller/AdminController.php',
        'controller_class' => 'AdminController',
        'controller_method' => 'alternarAtivoDenuncia',
        'http_method' => 'POST',
    ],
    // Processa exclusão lógica de comentário por admin.
    'processar_exclusao_comentario_admin' => [
        'type' => 'action',
        'controller_file' => 'controller/AdminController.php',
        'controller_class' => 'AdminController',
        'controller_method' => 'excluirComentario',
        'http_method' => 'POST',
    ],
    // Processa desativação de usuário por admin.
    'processar_exclusao_usuario_admin' => [
        'type' => 'action',
        'controller_file' => 'controller/AdminController.php',
        'controller_class' => 'AdminController',
        'controller_method' => 'excluirUsuario',
        'http_method' => 'POST',
    ],
    // Processa promoção de usuário para perfil admin.
    'processar_promocao_usuario_admin' => [
        'type' => 'action',
        'controller_file' => 'controller/AdminController.php',
        'controller_class' => 'AdminController',
        'controller_method' => 'promoverUsuarioAdmin',
        'http_method' => 'POST',
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
