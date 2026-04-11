<?php
// View do Formulário de Login
$tituloPagina = "Login";
?>
<?php include "view/templates/header.php"; ?>

<div class="auth-login-page">
    <section class="container-fluid px-0 auth-login-shell">
        <div class="row g-0 auth-login-grid">
            <div class="col-lg-7 d-none d-lg-block">
                <div class="auth-login-carousel-frame" data-auth-login-carousel>
                    <div class="auth-login-slide is-active" data-auth-login-slide
                        style="background-image: url('assets/images/Feira_Central_de_Ceilandia.png');"></div>
                    <div class="auth-login-slide" data-auth-login-slide
                        style="background-image: url('assets/images/ponte_jk.png');"></div>
                    <div class="auth-login-slide" data-auth-login-slide
                        style="background-image: url('assets/images/praca_relogio_taguatinga.png');"></div>

                    <div class="auth-login-carousel-overlay">
                        <p class="auth-login-kicker mb-2">Portal Institucional</p>
                        <h2 class="auth-login-hero mb-3">Construindo juntos o futuro da nossa cidade.</h2>
                        <p class="auth-login-hero-sub mb-0">Participe ativamente da zeladoria urbana e ajude a
                            transformar nosso território.</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="auth-login-panel">
                    <div class="auth-login-panel-inner">
                        <h2 class="auth-login-title mb-2">Bem-vindo de volta</h2>
                        <p class="text-body-secondary mb-4">Acesse sua conta para continuar contribuindo.</p>

                        <form action="index.php?rota=processar_login" method="POST" class="auth-login-form">
                            <?= csrfField() ?>

                            <div class="form-floating mb-3 auth-login-field">
                                <img src="assets/fonts/material-symbols/mail.svg" alt="" aria-hidden="true"
                                    class="auth-login-field-icon" width="18" height="18">
                                <input type="email" class="form-control" name="email" id="email" required
                                    autocomplete="email" placeholder=" ">
                                <label for="email">E-mail</label>
                            </div>

                            <div class="form-floating mb-3 auth-password-field auth-login-field">
                                <img src="assets/fonts/material-symbols/lock.svg" alt="" aria-hidden="true"
                                    class="auth-login-field-icon" width="18" height="18">
                                <input type="password" class="form-control" name="senha" id="senha" required
                                    autocomplete="current-password" placeholder=" ">
                                <label for="senha">Senha</label>
                                <button type="button" class="btn btn-sm auth-password-toggle"
                                    data-toggle-password="senha" aria-label="Mostrar senha" title="Mostrar senha">
                                    <img src="assets/fonts/material-symbols/visibility.svg" alt="" width="18"
                                        height="18" aria-hidden="true">
                                    <span class="visually-hidden">Mostrar senha</span>
                                </button>
                            </div>

                            <button class="btn btn-primary w-100 py-2 mt-2" type="submit">Entrar</button>
                        </form>

                        <p class="mt-4 mb-0 text-body-secondary text-center">Ainda não tem conta? <a
                                href="index.php?rota=cadastrar">Criar Conta</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="assets/js/auth-cadastro.js" defer></script>
<script src="assets/js/auth-login.js" defer></script>

<?php include "view/templates/footer.php"; ?>