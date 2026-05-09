<?php
$tituloPagina = "Alterar Senha";
?>
<?php include "view/templates/header.php"; ?>

<div class="d-flex min-vh-100 align-items-center py-4">
    <section class="container">
        <div class="text-center mb-4 auth-cadastro-header">
            <div class="auth-cadastro-header-icon mb-3">
                <img src="assets/fonts/material-symbols/lock.svg" alt="Alterar senha" width="24" height="24">
            </div>
            <h2 class="h1 mb-2 fw-bold">Alterar Senha</h2>
            <p class="mb-0 text-body-secondary">Digite sua nova senha. Você será redirecionado ao login após a
                alteração.</p>
        </div>

        <p class="text-center small text-body-secondary mb-3">
            A nova senha deve ter no mínimo 8 caracteres, com letra maiúscula, letra minúscula, número e caractere
            especial.
        </p>

        <form action="index.php?rota=processar_alterar_senha" method="POST" class="mx-auto auth-login-form"
            style="max-width:520px;">
            <?= csrfField() ?>

            <div class="form-floating mb-3 auth-password-field auth-login-field">
                <input type="password" class="form-control" name="nova_senha" id="nova_senha" required
                    autocomplete="new-password" placeholder=" ">
                <label for="nova_senha">Nova senha</label>
                <button type="button" class="btn btn-sm auth-password-toggle" data-toggle-password="nova_senha"
                    aria-label="Mostrar senha" title="Mostrar senha">
                    <img src="assets/fonts/material-symbols/visibility.svg" alt="" width="18" height="18"
                        aria-hidden="true">
                    <span class="visually-hidden">Mostrar senha</span>
                </button>
            </div>

            <div class="form-floating mb-3 auth-password-field auth-login-field">
                <input type="password" class="form-control" name="confirmacao_senha" id="confirmacao_senha" required
                    autocomplete="new-password" placeholder=" ">
                <label for="confirmacao_senha">Confirmar senha</label>
                <button type="button" class="btn btn-sm auth-password-toggle" data-toggle-password="confirmacao_senha"
                    aria-label="Mostrar confirmação de senha" title="Mostrar senha">
                    <img src="assets/fonts/material-symbols/visibility.svg" alt="" width="18" height="18"
                        aria-hidden="true">
                    <span class="visually-hidden">Mostrar confirmação de senha</span>
                </button>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Alterar senha</button>
                <a href="index.php?rota=login" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </section>
</div>

<script src="assets/js/auth-cadastro.js" defer></script>

<?php include "view/templates/footer.php"; ?>