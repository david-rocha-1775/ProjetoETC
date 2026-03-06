<?php
// Controller de Cadastro de Usuário
// Responsabilidade: receber dados do formulário, chamar o DAO, e redirecionar.
// SEM HTML e SEM SQL aqui!

require_once "model/dao/UsuarioDAO.php";

try {
    // 1. Pegar dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // 2. Criar o objeto DTO com os dados
    $usuario = new UsuarioDTO();
    $usuario->setNome($nome);
    $usuario->setEmail($email);
    $usuario->setSenha(password_hash($senha, PASSWORD_DEFAULT));

    // 3. Chamar o DAO para salvar no banco
    $usuarioDAO = new UsuarioDAO();
    $usuarioDAO->cadastrar($usuario);

    // 4. Redirecionar com mensagem de sucesso (via sessão)
    $_SESSION['mensagem'] = "Usuário cadastrado com sucesso!";
    $_SESSION['tipo_mensagem'] = "sucesso";
    header("Location: index.php?rota=login");
    exit();

} catch (Exception $e) {
    // Redirecionar com mensagem de erro (via sessão)
    $_SESSION['mensagem'] = "Erro ao cadastrar: " . $e->getMessage();
    $_SESSION['tipo_mensagem'] = "erro";
    header("Location: index.php?rota=cadastrar");
    exit();
}
?>