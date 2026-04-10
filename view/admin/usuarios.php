<?php
// View de listagem administrativa de usuários
?>
<?php include "view/templates/header.php"; ?>

<section class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Usuários Cadastrados</h2>
        <div class="d-flex gap-2">
            <a href="index.php?rota=admin_dashboard" class="btn btn-outline-primary">Dashboard Admin</a>
            <a href="index.php?rota=admin_denuncias" class="btn btn-outline-primary">Gerenciar Denúncias</a>
            <a href="index.php?rota=listar_categorias_admin" class="btn btn-outline-primary">Gerenciar Categorias</a>
            <a href="index.php?rota=painel" class="btn btn-outline-secondary">Voltar ao Painel</a>
        </div>
    </div>

    <?php if (!empty($usuarios)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Tipo</th>
                        <th>Data de Cadastro</th>
                        <th style="width: 180px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario->getId()) ?></td>
                            <td><?= htmlspecialchars($usuario->getNome()) ?></td>
                            <td><?= htmlspecialchars($usuario->getEmail()) ?></td>
                            <td><?= htmlspecialchars($usuario->getTipo()) ?></td>
                            <td><?= htmlspecialchars($usuario->getDataCadastro()) ?></td>
                            <td>
                                <?php if ((int) $usuario->getId() === (int) ($idUsuarioSessao ?? 0)): ?>
                                    <span class="badge text-bg-secondary">Conta Atual</span>
                                <?php else: ?>
                                    <form action="index.php?rota=processar_exclusao_usuario_admin" method="POST"
                                        onsubmit="return confirm('Tem certeza que deseja desativar este usuário?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id_usuario"
                                            value="<?= htmlspecialchars($usuario->getId()) ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Desativar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info mb-0" role="alert">
            Nenhum usuário encontrado.
        </div>
    <?php endif; ?>
</section>

<?php include "view/templates/footer.php"; ?>