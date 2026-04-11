<?php include "view/templates/header.php"; ?>

<section class="container py-4">
    <h2 class="mb-3">Gerenciamento de Categorias</h2>

    <div class="card mb-4">
        <div class="card-body">
            <h3 class="h6">Cadastrar nova categoria</h3>
            <form action="index.php?rota=processar_cadastro_categoria" method="POST" class="row g-2">
                <?= csrfField() ?>
                <div class="col-md-8">
                    <input type="text" name="nome_categoria" class="form-control" placeholder="Nome da categoria"
                        required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($categorias)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome da Categoria</th>
                        <th style="width: 420px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?= htmlspecialchars($categoria->getId()) ?></td>
                            <td><?= htmlspecialchars($categoria->getNomeCategoria()) ?></td>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <form action="index.php?rota=processar_edicao_categoria" method="POST"
                                        class="d-flex gap-2 flex-grow-1">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id_categoria"
                                            value="<?= htmlspecialchars($categoria->getId()) ?>">
                                        <input type="text" name="nome_categoria" class="form-control"
                                            value="<?= htmlspecialchars($categoria->getNomeCategoria()) ?>" required>
                                        <button type="submit" class="btn btn-outline-primary">Salvar</button>
                                    </form>

                                    <form action="index.php?rota=processar_exclusao_categoria" method="POST"
                                        onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id_categoria"
                                            value="<?= htmlspecialchars($categoria->getId()) ?>">
                                        <button type="submit" class="btn btn-outline-danger">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            Nenhuma categoria cadastrada.
        </div>
    <?php endif; ?>
</section>

<?php include "view/templates/footer.php"; ?>