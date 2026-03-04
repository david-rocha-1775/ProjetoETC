<?php
require_once "model/dao/Conexao.php";
try {
    $conexao = Conexao::getInstance();
    echo "<p>Conexão realizada com sucesso!</p>";
} catch (Exception $e) {
    echo "<p>Erro ao Conectar: " . $e->getMessage() . "</p>";
}
?>
<br>
<a href="index.php?rota=inicio">
    <button>Voltar para o Inicio</button>
</a>