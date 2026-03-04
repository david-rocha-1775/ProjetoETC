<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
</head>
<body>
    <h1>Cadastrar Usuário</h1>

    <form action="../control/CadastrarUsuarioControl.php" method="POST">

        <label>Nome:</label><br>
        <input type="text" name="nome" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>

        <button type="submit">Cadastrar</button>
    </form>

    <br>
    <a href="../index.php">
        <button>Voltar</button>
    </a>
</body>
</html>