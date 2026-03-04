<?php
require_once "../model/dao/conexao.php";

// Iniciamos a sessão para salvar o usuário logado
session_start();

try {
    // 1. Pegar dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // 2. Conectar ao banco
    $conexao = Conexao::getInstance();

    // 3. Preparar o comando SQL (SELECT)
    // Buscamos o usuário pelo e-mail
    $sql = "SELECT id_usuario, nome, senha FROM usuarios WHERE email = :email";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // 4. Verificar se o usuário foi encontrado
    if ($stmt->rowCount() > 0) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // 5. Verificar a senha 
        // Se você usou password_hash no cadastro, use password_verify aqui.
        // Se salvou texto puro (não recomendado), use: if ($senha == $usuario['senha'])
        if ($senha == $usuario['senha']) {
            
            // Login com sucesso! Guardamos os dados na sessão
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_nome'] = $usuario['nome'];

            echo "<p>Login realizado com sucesso! Bem-vindo, " . $usuario['nome'] . ".</p>";
            echo "<a href='../view/PainelUsuario.php'><button>Ir para o Painel</button></a>";
            
        } else {
            echo "<p style='color:red;'>Senha incorreta.</p>";
            echo "<a href='../view/LoginUsuarioView.php'><button>Tentar novamente</button></a>";
        }
    } else {
        echo "<p style='color:red;'>Usuário não encontrado.</p>";
        echo "<a href='../view/LoginUsuarioView.php'><button>Tentar novamente</button></a>";
    }

} catch (Exception $e) {
    echo "<p>Erro ao processar login: " . $e->getMessage() . "</p>";
}
?>