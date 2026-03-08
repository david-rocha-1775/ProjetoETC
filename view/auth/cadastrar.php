<?php $tituloPagina = "Cadastro"; ?>
<?php include "view/templates/header.php"; ?>

<section>
    <h2>Cadastrar Usuário</h2>

    <form action="index.php?rota=processar_cadastro" method="POST">

        <label for="nome">Nome:</label><br>
        <input type="text" name="nome" id="nome" required placeholder="Digite seu nome">
        <br><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" required placeholder="Digite seu email">
        <br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" name="senha" id="senha" required placeholder="Digite sua senha">
        <br><br>

        <button type="submit">Cadastrar</button>
        <button type="reset">Limpar</button>
    </form>

    <hr>
    <p>Já tem conta?
        <a href="index.php?rota=login">Faça login aqui</a>
    </p>
</section>

<?php include "view/templates/footer.php"; ?>