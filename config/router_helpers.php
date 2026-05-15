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
 * Retorna (e cria quando necessário) o token CSRF da sessão atual.
 *
 * @return string
 */
function csrfToken()
{
    if (!isset($_SESSION['_csrf_token']) || !is_string($_SESSION['_csrf_token']) || $_SESSION['_csrf_token'] === '') {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf_token'];
}

/**
 * Renderiza campo hidden com token CSRF para formulários.
 */
function csrfField()
{
    $token = htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="_csrf_token" value="' . $token . '">';
}

/**
 * Valida token CSRF enviado por formulário ou header.
 *
 * @return bool
 */
function csrfRequisicaoValida()
{
    $tokenSessao = $_SESSION['_csrf_token'] ?? '';
    if (!is_string($tokenSessao) || $tokenSessao === '') {
        return false;
    }

    $tokenRequisicao = $_POST['_csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!is_string($tokenRequisicao) || $tokenRequisicao === '') {
        return false;
    }

    return hash_equals($tokenSessao, $tokenRequisicao);
}

/**
 * Renderiza página de token CSRF inválido.
 */
function responder419()
{
    http_response_code(419);
    echo "<h1>Erro 419 - Sessão expirada ou token inválido</h1>";
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
