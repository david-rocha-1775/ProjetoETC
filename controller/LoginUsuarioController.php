<?php
// Controller de Login do Usuário
// Responsabilidade: receber dados do formulário, validar com o DAO, e redirecionar.
// SEM HTML e SEM SQL aqui!

require_once "model/dao/UsuarioDAO.php";

try {
    // 1. Pegar dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // 2. Buscar usuário pelo e-mail usando o DAO
    $usuarioDAO = new UsuarioDAO();
    $usuario = $usuarioDAO->buscarPorEmail($email);

    // 3. Verificar se o usuário foi encontrado
    if ($usuario !== null) {

        // 4. Verificar a senha
        // Compara a senha digitada com o hash salvo no banco
        if (password_verify($senha, $usuario->getSenha())) {

            // Login com sucesso! Guardamos os dados na sessão
            $_SESSION['usuario_id'] = $usuario->getId();
            $_SESSION['usuario_nome'] = $usuario->getNome();
            $_SESSION['logado'] = true;

            // Redireciona para o painel
            header("Location: index.php?rota=painel");
            exit();

        } else {
            $_SESSION['mensagem'] = "Senha incorreta.";
            $_SESSION['tipo_mensagem'] = "erro";
            header("Location: index.php?rota=login");
            exit();
        }
    } else {
        $_SESSION['mensagem'] = "Usuário não encontrado.";
        $_SESSION['tipo_mensagem'] = "erro";
        header("Location: index.php?rota=login");
        exit();
    }

} catch (Exception $e) {
    $_SESSION['mensagem'] = "Erro ao processar login: " . $e->getMessage();
    $_SESSION['tipo_mensagem'] = "erro";
    header("Location: index.php?rota=login");
    exit();
}
?>