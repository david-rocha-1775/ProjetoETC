<?php $tituloPagina = "Cadastro"; ?>
<?php include "view/templates/header.php"; ?>

<div class="auth-cadastro-page d-flex min-vh-100 align-items-lg-center py-4">
    <section class="container">
        <form action="index.php?rota=processar_cadastro" method="POST">
            <?= csrfField() ?>

            <div class="text-center mb-4 auth-cadastro-header">
                <div class="auth-cadastro-header-icon mb-3">
                    <img src="assets/fonts/material-symbols/how_to_reg.svg" alt="Cadastro" width="24" height="24">
                </div>
                <h2 class="h1 mb-2 fw-bold">Cidade Atenta</h2>
                <p class="mb-0 text-body-secondary">Junte-se a nossa rede de cidadania ativa e participe da sua
                    comunidade.</p>
            </div>

            <div class="row g-3 g-lg-4">
                <div class="col-12 col-lg-7">
                    <article class="auth-cadastro-card h-100">
                        <div class="auth-cadastro-card-title">
                            <img src="assets/fonts/material-symbols/badge.svg" alt="Identificacao" width="20"
                                height="20">
                            <h3 class="h5 mb-0">Identificação</h3>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="nome" id="nome" required autocomplete="name"
                                placeholder="Digite seu nome completo">
                            <label for="nome">Nome Completo</label>
                        </div>

                        <div class="form-floating mb-0">
                            <input type="email" class="form-control" name="email" id="email" required
                                autocomplete="email" placeholder="Digite seu email">
                            <label for="email">E-mail</label>
                        </div>
                    </article>
                </div>

                <div class="col-12 col-lg-5">
                    <article class="auth-cadastro-card h-100">
                        <div class="auth-cadastro-card-title">
                            <img src="assets/fonts/material-symbols/lock.svg" alt="Seguranca" width="20" height="20">
                            <h3 class="h5 mb-0">Segurança</h3>
                        </div>

                        <div class="form-floating mb-3 auth-password-field">
                            <input type="password" class="form-control" name="senha" id="senha" required
                                autocomplete="new-password" placeholder="Digite sua senha">
                            <label for="senha">Senha</label>
                            <button type="button" class="btn btn-sm auth-password-toggle" data-toggle-password="senha"
                                aria-label="Mostrar senha" title="Mostrar senha">
                                <img src="assets/fonts/material-symbols/visibility.svg" alt="" width="18" height="18"
                                    aria-hidden="true">
                                <span class="visually-hidden">Mostrar senha</span>
                            </button>
                        </div>

                        <div class="form-floating mb-0 auth-password-field">
                            <input type="password" class="form-control" name="confirmacao_senha" id="confirmacao_senha"
                                required autocomplete="new-password" placeholder="Confirme sua senha">
                            <label for="confirmacao_senha">Confirmar Senha</label>
                            <button type="button" class="btn btn-sm auth-password-toggle"
                                data-toggle-password="confirmacao_senha" aria-label="Mostrar confirmação de senha"
                                title="Mostrar senha">
                                <img src="assets/fonts/material-symbols/visibility.svg" alt="" width="18" height="18"
                                    aria-hidden="true">
                                <span class="visually-hidden">Mostrar confirmação de senha</span>
                            </button>
                        </div>
                    </article>
                </div>
            </div>

            <div class="row g-3 g-lg-4 mt-1">
                <div class="col-12 ms-lg-auto col-lg-5">
                    <article class="auth-cadastro-card auth-cadastro-cta h-100">
                        <div class="auth-cadastro-card-title mb-2">
                            <img src="assets/fonts/material-symbols/group.svg" alt="Criar conta" width="20" height="20">
                            <h3 class="h5 mb-0">Pronto para começar?</h3>
                        </div>
                        <p class="small text-body-secondary mb-3">
                            Após o cadastro, você pode acessar o painel, acompanhar suas denúncias e interagir com a
                            comunidade.
                        </p>

                        <div class="form-check text-start mb-3">
                            <input class="form-check-input" type="checkbox" value="1" name="aceite_termos"
                                id="agreeTerms" required>
                            <label class="form-check-label" for="agreeTerms">
                                Concordo com os termos de uso.
                            </label>
                        </div>

                        <button class="btn btn-primary w-100 py-2 mb-2" type="submit">Criar Conta</button>
                        <button class="btn btn-secondary w-100 py-2" type="reset">Limpar</button>
                    </article>
                </div>
            </div>

            <p class="mt-4 mb-2 text-body-secondary text-center">Já possui uma conta? <a
                    href="index.php?rota=login">Entrar no Portal</a></p>

        </form>
    </section>
</div>

<script src="assets/js/auth-cadastro.js" defer></script>

<?php include "view/templates/footer.php"; ?>