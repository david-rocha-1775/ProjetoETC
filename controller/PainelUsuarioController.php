<?php
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: index.php?rota=login");
    exit();
}

require_once "model/dao/Conexao.php";

try {
    $conexao = Conexao::getInstance();

    // Buscamos as denúncias
    $sql = "SELECT titulo, descricao, localizacao, foto_path, status FROM denuncias ORDER BY id_denuncia DESC LIMIT 10";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    
    $denuncias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. SÓ DEPOIS de pegar os dados e a sessão, chamamos a View
    include "view/PainelUsuarioView.php";

} catch (Exception $e) {
    echo "Erro ao carregar os dados: " . $e->getMessage();
}
?>