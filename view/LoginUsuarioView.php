<?php $tituloPagina = "Login"; ?>
<?php include "view/templates/header.php"; ?>

<section>
    <h2>Acessar Sistema</h2>

    <form action="index.php?rota=processar_login" method="POST">

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
        <a href="index.php?rota=cadastrar">Cadastre-se aqui</a>
    </p>
</section>

<?php include "view/templates/footer.php"; ?>