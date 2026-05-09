<?php
$tituloPagina = "Recuperar Senha";
?>
<?php include "view/templates/header.php"; ?>

<div class="d-flex min-vh-100 align-items-center py-4">
    <section class="container">
        <div class="text-center mb-4 auth-cadastro-header">
            <div class="auth-cadastro-header-icon mb-3">
                <img src="assets/fonts/material-symbols/mail.svg" alt="Recuperar senha" width="24" height="24">
            </div>
            <h2 class="h1 mb-2 fw-bold">Recuperar Senha</h2>
            <p class="mb-0 text-body-secondary">Informe seu e-mail para iniciar o processo de alteração de senha.</p>
        </div>

        <form action="index.php?rota=processar_recuperar_senha" method="POST" class="mx-auto auth-login-form"
            style="max-width:520px;">
            <?= csrfField() ?>

            <div class="form-floating mb-3 auth-login-field">
                <input type="email" class="form-control" name="email" id="email" required autocomplete="email"
                    placeholder=" ">
                <label for="email">E-mail</label>
            </div>

            <div class="form-floating mb-3 auth-login-field">
                <input type="email" class="form-control" name="confirmacao_email" id="confirmacao_email" required
                    autocomplete="email" placeholder=" ">
                <label for="confirmacao_email">Confirme o e-mail</label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Prosseguir</button>
                <a href="index.php?rota=login" class="btn btn-outline-secondary">Voltar</a>
            </div>
        </form>
    </section>
</div>

<?php include "view/templates/footer.php"; ?>