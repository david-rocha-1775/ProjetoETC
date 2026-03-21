<?php
// View de gerenciamento do perfil do usuário autenticado
?>
<?php include "view/templates/header.php"; ?>

<section class="container py-4">
    <h2 class="mb-3">Meu Perfil</h2>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h3 class="h5 mb-3">Atualizar Dados</h3>

                    <form action="index.php?rota=processar_edicao_usuario" method="POST">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome"
                                value="<?= htmlspecialchars($usuario->getNome()) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($usuario->getEmail()) ?>" required>
                        </div>

                        <hr>
                        <p class="mb-2"><strong>Troca de senha (opcional)</strong></p>

                        <div class="mb-3">
                            <label for="senha_atual" class="form-label">Senha Atual</label>
                            <input type="password" class="form-control" id="senha_atual" name="senha_atual">
                        </div>

                        <div class="mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" id="nova_senha" name="nova_senha">
                        </div>

                        <div class="mb-3">
                            <label for="confirmacao_senha" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" class="form-control" id="confirmacao_senha" name="confirmacao_senha">
                        </div>

                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        <a href="index.php?rota=painel" class="btn btn-outline-secondary">Voltar ao Painel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-danger">
                <div class="card-body">
                    <h3 class="h5 text-danger">Excluir Conta</h3>
                    <p class="mb-3">Esta ação é definitiva. Para confirmar, informe sua senha.</p>

                    <form action="index.php?rota=processar_exclusao_usuario" method="POST"
                        onsubmit="return confirm('Tem certeza que deseja excluir sua conta? Essa ação não pode ser desfeita.');">
                        <div class="mb-3">
                            <label for="senha_confirmacao" class="form-label">Senha de Confirmação</label>
                            <input type="password" class="form-control" id="senha_confirmacao" name="senha_confirmacao"
                                required>
                        </div>

                        <button type="submit" class="btn btn-danger">Excluir Minha Conta</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "view/templates/footer.php"; ?>