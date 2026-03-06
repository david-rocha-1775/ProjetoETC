<?php
// Controller para testar a conexão com o banco de dados
// Usado apenas para desenvolvimento/debug

require_once "model/dao/Conexao.php";

try {
    $conexao = Conexao::getInstance();
    $_SESSION['mensagem'] = "Conexão realizada com sucesso!";
    $_SESSION['tipo_mensagem'] = "sucesso";
} catch (Exception $e) {
    $_SESSION['mensagem'] = "Erro ao Conectar: " . $e->getMessage();
    $_SESSION['tipo_mensagem'] = "erro";
}

// header("Location: index.php?rota=inicio");
exit();
?>