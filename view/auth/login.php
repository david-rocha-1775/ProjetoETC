<?php
// View do Formulário de Login
$tituloPagina = "Login";
?>
<?php include "view/templates/header.php"; ?>

<div class="d-flex py-4" style="min-height: calc(100vh - 120px);">
    <section class="form-signin w-100 m-auto" style="max-width: 330px;">
        <form action="index.php?rota=processar_login" method="POST">
            <?= csrfField() ?>

            <h2 class="h3 mb-3 fw-normal">Acessar Sistema</h2>

            <div class="form-floating mb-3">
                <input type="email" class="form-control" name="email" id="email" required
                    placeholder="Digite seu e-mail">
                <label for="email">E-mail</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control" name="senha" id="senha" required
                    placeholder="Digite sua senha">
                <label for="senha">Senha</label>
            </div>

            <button class="btn btn-primary w-100 py-2 mb-2" type="submit">Entrar</button>
            <button class="btn btn-secondary w-100 py-2 mb-2" type="reset">Limpar</button>

        </form>

        <hr>

        <p class="mt-5 mb-3 text-body-secondary">Ainda não tem conta?

            <a href="index.php?rota=cadastrar">Cadastre-se aqui</a>
        </p>
    </section>
</div>

<?php include "view/templates/footer.php"; ?>