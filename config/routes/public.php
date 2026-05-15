<?php

// Rotas públicas sem necessidade de autenticação.
return [
    // Página inicial pública.
    'inicio' => [
        'type' => 'view',
        'target' => 'view/public/home.php',
        'http_method' => 'GET',
    ],
];
