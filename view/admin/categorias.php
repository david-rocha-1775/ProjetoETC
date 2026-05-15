<?php include "view/templates/header.php"; ?>

<?php
$filtrosCategoriasAtuais = is_array($filtrosCategoriasAtuais ?? null) ? $filtrosCategoriasAtuais : [
    'ativo' => 1,
];
?>

<div class="container py-4 painel-shell">
    <section class="painel-conteudo-col">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
                <h2 class="mb-1">Gerenciamento de Categorias</h2>
            </div>
        </div>

        <div class="card shadow-sm painel-filtros-card mb-4">
            <div class="card-body">
                <h3 class="h6 mb-3">Filtros da listagem</h3>
                <form action="index.php" method="GET" class="painel-filtros-form">
                    <input type="hidden" name="rota" value="listar_categorias_admin">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-4">
                            <label for="filtroCategoriaAtivo" class="form-label">Status</label>
                            <select id="filtroCategoriaAtivo" name="ativo" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="1" <?= (string) ($filtrosCategoriasAtuais['ativo'] ?? 1) === '1' ? 'selected' : '' ?>>Ativos</option>
                                <option value="0" <?= (string) ($filtrosCategoriasAtuais['ativo'] ?? 1) === '0' ? 'selected' : '' ?>>Inativos</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                            <a href="index.php?rota=listar_categorias_admin"
                                class="btn btn-outline-secondary btn-sm">Limpar filtros</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm painel-filtros-card mb-4">
            <div class="card-body">
                <h3 class="h6 mb-3">Cadastrar nova categoria</h3>
                <form action="index.php?rota=processar_cadastro_categoria" method="POST" class="painel-filtros-form">
                    <?= csrfField() ?>
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-9">
                            <label for="novaCategoriaNome" class="form-label">Nome da categoria</label>
                            <input type="text" id="novaCategoriaNome" name="nome_categoria"
                                class="form-control form-control-sm" placeholder="Digite o nome da categoria" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Cadastrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($categorias)): ?>
            <section class="mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h3 class="mb-0">Categorias cadastradas</h3>
                    <span class="text-muted small">Total:
                        <strong><?= htmlspecialchars((string) count($categorias), ENT_QUOTES, 'UTF-8') ?></strong></span>
                </div>

                <div class="painel-denuncias-lista mb-3">
                    <?php foreach ($categorias as $categoria): ?>
                        <?php $categoriaAtiva = (int) $categoria->getAtivo() === 1; ?>
                        <article class="card shadow-sm painel-metrica-card">
                            <div class="card-body">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-transparent border border-secondary-subtle text-body">ID
                                            <?= htmlspecialchars((string) $categoria->getId(), ENT_QUOTES, 'UTF-8') ?></span>
                                        <h4 class="h5 mb-0">
                                            <?= htmlspecialchars((string) $categoria->getNomeCategoria(), ENT_QUOTES, 'UTF-8') ?>
                                        </h4>
                                        <span class="badge <?= $categoriaAtiva ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                            <?= $categoriaAtiva ? 'Ativa' : 'Inativa' ?>
                                        </span>
                                    </div>

                                    <div class="d-flex flex-column flex-md-row gap-2 w-100 w-lg-auto">
                                        <?php if ($categoriaAtiva): ?>
                                            <form action="index.php?rota=processar_edicao_categoria" method="POST"
                                                class="d-flex gap-2 flex-grow-1 painel-filtros-form">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="id_categoria"
                                                    value="<?= htmlspecialchars((string) $categoria->getId(), ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="text" name="nome_categoria" class="form-control form-control-sm"
                                                    value="<?= htmlspecialchars((string) $categoria->getNomeCategoria(), ENT_QUOTES, 'UTF-8') ?>"
                                                    required>
                                                <button type="submit" class="btn btn-outline-primary btn-sm">Salvar</button>
                                            </form>

                                            <form action="index.php?rota=processar_exclusao_categoria" method="POST"
                                                onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?');">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="id_categoria"
                                                    value="<?= htmlspecialchars((string) $categoria->getId(), ENT_QUOTES, 'UTF-8') ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">Excluir</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge text-bg-light border">Sem ações para categoria inativa</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                Nenhuma categoria cadastrada.
            </div>
        <?php endif; ?>
    </section>
</div>

<?php include "view/templates/footer.php"; ?>