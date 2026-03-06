<?php
// Controller do Painel do Usuário
// Responsabilidade: verificar autenticação, buscar dados com DAO, e chamar a View.
// SEM HTML e SEM SQL aqui!

// 1. Verificar se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: index.php?rota=login");
    exit();
}

require_once "model/dao/DenunciaDAO.php";

try {
    // 2. Buscar as denúncias usando o DAO
    $denunciaDAO = new DenunciaDAO();
    $denuncias = $denunciaDAO->listarUltimas(10);

    // 3. Chamar a View com os dados prontos
    $tituloPagina = "Painel do Usuário";
    include "view/PainelUsuarioView.php";

} catch (Exception $e) {
    $_SESSION['mensagem'] = "Erro ao carregar os dados: " . $e->getMessage();
    $_SESSION['tipo_mensagem'] = "erro";
    header("Location: index.php?rota=inicio");
    exit();
}
?>