<?php

class ResponseHelper
{
    /**
     * Envia uma resposta JSON e encerra execução.
     *
     * @param array $dados
     * @param int $statusCode
     * @return void
     */
    public static function responderJson(array $dados, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
}
?>
