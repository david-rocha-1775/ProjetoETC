<?php

trait ValidadorRequisicao
{
    /**
     * Garante que a requisição seja POST antes de processar a ação.
     *
     * @param string $rotaRetorno
     * @return void
     */
    protected function exigirMetodoPost($rotaRetorno)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (method_exists($this, 'redirecionarComErro')) {
                $this->redirecionarComErro("Método inválido.", $rotaRetorno);
            } else {
                $_SESSION['mensagem'] = "Método inválido.";
                $_SESSION['tipo_mensagem'] = "erro";
                header("Location: index.php?rota=" . $rotaRetorno);
                exit();
            }
        }
    }
}
?>