<?php

// Rotas do painel para usuários autenticados.
return [
    // Exibe dashboard com últimas denúncias.
    'painel' => [
        'type' => 'action',
        'controller_file' => 'controller/PainelController.php',
        'controller_class' => 'PainelController',
        'controller_method' => 'index',
        'http_method' => 'GET',
    ],
    // Exibe e processa o formulário de nova denúncia.
    'nova_denuncia' => [
        'type' => 'action',
        'controller_file' => 'controller/PainelController.php',
        'controller_class' => 'PainelController',
        'controller_method' => 'cadastrarDenuncia',
        'http_method' => ['GET', 'POST'],
    ],
    // Processa edição de denúncia existente.
    'processar_edicao_denuncia' => [
        'type' => 'action',
        'controller_file' => 'controller/PainelController.php',
        'controller_class' => 'PainelController',
        'controller_method' => 'atualizarDenuncia',
        'http_method' => 'POST',
    ],
    // Processa exclusão de denúncia existente.
    'processar_exclusao_denuncia' => [
        'type' => 'action',
        'controller_file' => 'controller/PainelController.php',
        'controller_class' => 'PainelController',
        'controller_method' => 'excluirDenuncia',
        'http_method' => 'POST',
    ],
];
