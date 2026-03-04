<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de Usuário</title>
</head>
<body>
    <h1>Acessar Sistema</h1>

    <form action="../control/LoginUsuarioControl.php" method="POST">
        
        <label for="email">E-mail:</label><br>
        <input type="email" name="email" id="email" required placeholder="Digite seu e-mail">
        <br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" name="senha" id="senha" required placeholder="Digite sua senha">
        <br><br>

        <button type="submit">Entrar</button>
        <button type="reset">Limpar</button>

    </form>

    <hr>
    
    <p>Ainda não tem conta? 
        <a href="CadastrarUsuarioView.php">Cadastre-se aqui</a>
    </p>
    
    <a href="../index.php">
        <button>voltar</button>
    </a>

</body>
</html>