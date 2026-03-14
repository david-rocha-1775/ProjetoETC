<?php $tituloPagina = "Cadastro"; ?>
<?php include "view/templates/header.php"; ?>

<div class="d-flex align-items-center py-4 bg-body-tertiary" style="min-height: calc(100vh - 120px);">
    <section class="form-signin w-100 m-auto" style="max-width: 330px;">
        <form action="index.php?rota=processar_cadastro" method="POST">

            <h2 class="h3 mb-3 fw-normal">Criar Conta</h2>

            <div class="form-floating">
                <input type="text" class="form-control mb-2" name="nome" id="nome" required
                    placeholder="Digite seu nome completo">
                <label for="nome">Nome Completo</label>
            </div>

            <div class="form-floating">
                <input type="email" class="form-control mb-2" name="email" id="email" required
                    placeholder="Digite seu email">
                <label for="email">Email</label>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control mb-2" name="senha" id="senha" required
                    placeholder="Digite sua senha">
                <label for="senha">Senha</label>
            </div>

            <div class="form-check text-start my-3">
                <input class="form-check-input" type="checkbox" value="agree-terms" id="agreeTerms">
                <label class="form-check-label" for="agreeTerms">
                    Concordo com os termos
                </label>
            </div>


            <button class="btn btn-primary w-100 py-2 mb-2" type="submit">Cadastrar</button>
            <button class="btn btn-secondary w-100 py-2" type="reset">Limpar</button>

            <p class="mt-3 mb-3 text-body-secondary">Já tem conta? <a href="index.php?rota=login">Faça login aqui</a>
            </p>

        </form>
    </section>
</div>

<?php include "view/templates/footer.php"; ?>