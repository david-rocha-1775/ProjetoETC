<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
</head>
<body>
    <h1>Cadastrar Usuário</h1>

    <form action="../control/CadastrarUsuarioControl.php" method="POST">

        <label for="nome">Nome:</label><br>
        <input type="text" name="nome" id="nome" required placeholder="digite seu nome">
        <br><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" required placeholder="digite seu email">
        <br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" name="senha" id="senha" required placeholder="digite sua senha">
        <br><br>

        <button type="submit">Cadastrar</button>
        <button type="reset">Limpar</button>
    </form>

    <hr>
    <p>Já tem conta? 
        <a href="LoginUsuarioView.php">Faça login aqui</a>
    </p>
    <a href="../index.php">
        <button>Voltar</button>
    </a>
</body>
</html>