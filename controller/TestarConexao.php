<?php
require_once "../model/dao/Conexao.php";
try {
    $conexao = conexao::getInstance();
    echo "<p>Conexão realizada com sucesso!</p>";
} catch (Exception $e) {
    echo "<p>Erro ao Conectar: " . $e->getMessage() . "</p>";
}
?>
<br>
<a href="../index.php">
    <button>Voltar para o Inicio</button>
</a>