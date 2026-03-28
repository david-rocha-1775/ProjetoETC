<?php

/**
 * Verifica se o método HTTP atual é permitido para a rota.
 *
 * @param string|array $allowedMethods
 * @param string $requestMethod
 * @return bool
 */
function metodoPermitido($allowedMethods, $requestMethod)
{
    if ($allowedMethods === 'ANY') {
        return true;
    }

    if (is_string($allowedMethods)) {
        return strtoupper($allowedMethods) === strtoupper($requestMethod);
    }

    if (is_array($allowedMethods)) {
        $allowedMethods = array_map('strtoupper', $allowedMethods);
        return in_array(strtoupper($requestMethod), $allowedMethods, true);
    }

    return false;
}

/**
 * Renderiza página de rota não encontrada.
 */
function responder404()
{
    http_response_code(404);
    echo "<h1>Erro 404 - Página não encontrada</h1>";
    echo "<a href='index.php?rota=inicio'>Voltar ao Início</a>";
}

/**
 * Renderiza página de método não permitido.
 */
function responder405()
{
    http_response_code(405);
    echo "<h1>Erro 405 - Método não permitido</h1>";
    echo "<a href='index.php?rota=inicio'>Voltar ao Início</a>";
}

/**
 * Renderiza página de erro interno de roteamento.
 */
function responder500()
{
    http_response_code(500);
    echo "<h1>Erro 500 - Falha interna no roteamento</h1>";
    echo "<a href='index.php?rota=inicio'>Voltar ao Início</a>";
}
