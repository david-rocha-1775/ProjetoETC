<?php
// Arquivo de Roteamento (Front Controller)
// Ponto de entrada do sistema que direciona a requisição para o Controller específico

session_start();
require_once 'config/router_helpers.php';

// Lista de arquivos de rotas por contexto (público, autenticação, painel e admin)
$arquivosDeRota = [
    'config/routes/public.php',
    'config/routes/auth.php',
    'config/routes/painel.php',
    'config/routes/admin.php',
];

// Carrega e consolida todas as rotas em uma whitelist única
$rotas = [];
foreach ($arquivosDeRota as $arquivoDeRota) {
    $grupo = require $arquivoDeRota;
    if (is_array($grupo)) {
        $rotas = array_merge($rotas, $grupo);
    }
}

// Resolve a rota solicitada e o método HTTP da requisição atual
$rota = isset($_GET['rota']) ? $_GET['rota'] : 'inicio';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Se a rota não existir na whitelist, retorna 404
if (!isset($rotas[$rota])) {
    responder404();
    exit();
}

$config = $rotas[$rota];
$allowedMethods = $config['http_method'] ?? 'GET';

// Se o método HTTP não for permitido para a rota, retorna 405
if (!metodoPermitido($allowedMethods, $requestMethod)) {
    responder405();
    exit();
}

try {
    // O tipo define como a rota será executada: view, script ou action (controller)
    $type = $config['type'] ?? '';

    if ($type === 'view') {
        include $config['target'];
        exit();
    }

    if ($type === 'script') {
        require $config['target'];
        exit();
    }

    if ($type === 'action') {
        if ($requestMethod === 'POST' && !csrfRequisicaoValida()) {
            responder419();
            exit();
        }

        $controllerFile = $config['controller_file'] ?? '';
        $controllerClass = $config['controller_class'] ?? '';
        $controllerMethod = $config['controller_method'] ?? '';

        // Garante que a configuração da ação está completa
        if ($controllerFile === '' || $controllerClass === '' || $controllerMethod === '') {
            throw new RuntimeException('Configuração de rota inválida.');
        }

        require_once $controllerFile;

        // Verifica se controller e método existem antes de executar
        if (!class_exists($controllerClass, false)) {
            throw new RuntimeException('Controller não encontrado.');
        }

        $controller = new $controllerClass();
        if (!method_exists($controller, $controllerMethod)) {
            throw new RuntimeException('Método de controller não encontrado.');
        }

        $controller->{$controllerMethod}();
        exit();
    }

    throw new RuntimeException('Tipo de rota inválido.');

} catch (Throwable $e) {
    // Qualquer falha inesperada no roteamento retorna erro interno padronizado
    responder500();
}
?>