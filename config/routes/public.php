<?php

// Rotas públicas sem necessidade de autenticação.
return [
    // Página inicial pública.
    'inicio' => [
        'type' => 'view',
        'target' => 'view/public/home.php',
        'http_method' => 'GET',
    ],
    // Script utilitário para validar conexão com o banco.
    'testar_conexao' => [
        'type' => 'script',
        'target' => 'controller/TestarConexao.php',
        'http_method' => 'GET',
    ],
];
