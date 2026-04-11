<?php include "view/templates/header.php"; ?>

<section class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Gerenciamento de Denúncias</h2>
        <div class="d-flex gap-2">
            <a href="index.php?rota=admin_dashboard" class="btn btn-outline-primary">Dashboard Admin</a>
            <a href="index.php?rota=listar_usuarios" class="btn btn-outline-primary">Gerenciar Usuários</a>
            <a href="index.php?rota=listar_categorias_admin" class="btn btn-outline-primary">Gerenciar Categorias</a>
            <a href="index.php?rota=painel" class="btn btn-outline-secondary">Voltar ao Painel</a>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h3 class="h6 mb-3">Filtros</h3>
            <form action="index.php" method="GET" class="row g-2 align-items-end">
                <input type="hidden" name="rota" value="admin_denuncias">

                <div class="col-12 col-md-3">
                    <label for="filtroStatus" class="form-label">Status</label>
                    <select id="filtroStatus" name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="Aberto" <?= ($filtrosAtuais['status'] ?? '') === 'Aberto' ? 'selected' : '' ?>>
                            Aberto</option>
                        <option value="Em Andamento" <?= ($filtrosAtuais['status'] ?? '') === 'Em Andamento' ? 'selected' : '' ?>>Em Andamento</option>
                        <option value="Resolvido" <?= ($filtrosAtuais['status'] ?? '') === 'Resolvido' ? 'selected' : '' ?>>Resolvido</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label for="filtroCategoria" class="form-label">Categoria</label>
                    <select id="filtroCategoria" name="categoria" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= htmlspecialchars((string) $categoria->getId(), ENT_QUOTES, 'UTF-8') ?>"
                                <?= (int) ($filtrosAtuais['categoria'] ?? 0) === (int) $categoria->getId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $categoria->getNomeCategoria(), ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label for="filtroBusca" class="form-label">Busca</label>
                    <input type="text" id="filtroBusca" name="busca" class="form-control"
                        value="<?= htmlspecialchars((string) ($filtrosAtuais['busca'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="120" placeholder="Título, descrição ou local">
                </div>

                <div class="col-6 col-md-1">
                    <label for="filtroLimite" class="form-label">Limite</label>
                    <select id="filtroLimite" name="limite" class="form-select">
                        <?php foreach ([10, 20, 50] as $opcaoLimite): ?>
                            <option value="<?= $opcaoLimite ?>" <?= (int) ($filtrosAtuais['limite'] ?? 10) === $opcaoLimite ? 'selected' : '' ?>>
                                <?= $opcaoLimite ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label for="filtroOrdem" class="form-label">Ordem</label>
                    <select id="filtroOrdem" name="ordem" class="form-select">
                        <option value="recentes" <?= ($filtrosAtuais['ordem'] ?? 'recentes') === 'recentes' ? 'selected' : '' ?>>Mais recentes</option>
                        <option value="antigas" <?= ($filtrosAtuais['ordem'] ?? '') === 'antigas' ? 'selected' : '' ?>>Mais
                            antigas</option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="index.php?rota=admin_denuncias" class="btn btn-outline-secondary">Limpar filtros</a>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="mb-0 text-muted">
            Total encontrado: <?= htmlspecialchars((string) $totalDenuncias, ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>

    <?php if (!empty($denuncias)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Categoria</th>
                        <th>Autor</th>
                        <th>Data</th>
                        <th style="width: 300px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($denuncias as $denuncia): ?>
                        <?php
                        $idDenuncia = (int) $denuncia->getId();
                        $idUsuario = (int) $denuncia->getIdUsuario();
                        $idCategoria = (int) $denuncia->getIdCategoria();
                        $nomeUsuario = $mapaUsuarios[$idUsuario] ?? 'Usuário não identificado';
                        $nomeCategoria = $mapaCategorias[$idCategoria] ?? 'Categoria indisponível';
                        $comentarios = $interacoesDenuncias[$idDenuncia] ?? [];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars((string) $idDenuncia, ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <strong><?= htmlspecialchars((string) $denuncia->getTitulo(), ENT_QUOTES, 'UTF-8') ?></strong>
                                <div class="small text-muted">
                                    <?= htmlspecialchars((string) $denuncia->getLocalizacao(), ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge text-bg-secondary">
                                    <?= htmlspecialchars((string) $denuncia->getStatus(), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars((string) $nomeCategoria, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $nomeUsuario, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $denuncia->getDataCriacao(), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <form action="index.php?rota=processar_status_denuncia_admin" method="POST"
                                    class="d-flex flex-wrap gap-2 mb-2">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id_denuncia"
                                        value="<?= htmlspecialchars((string) $idDenuncia, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="retorno_filtros"
                                        value="<?= htmlspecialchars((string) $queryFiltrosComPaginaAtual, ENT_QUOTES, 'UTF-8') ?>">

                                    <select name="status" class="form-select form-select-sm" style="min-width: 140px;" required>
                                        <option value="Aberto" <?= $denuncia->getStatus() === 'Aberto' ? 'selected' : '' ?>>Aberto
                                        </option>
                                        <option value="Em Andamento" <?= $denuncia->getStatus() === 'Em Andamento' ? 'selected' : '' ?>>Em Andamento</option>
                                        <option value="Resolvido" <?= $denuncia->getStatus() === 'Resolvido' ? 'selected' : '' ?>>
                                            Resolvido</option>
                                    </select>
                                    <button type="submit" class="btn btn-outline-primary btn-sm">Salvar Status</button>
                                </form>

                                <details>
                                    <summary class="small">Comentários
                                        (<?= htmlspecialchars((string) count($comentarios), ENT_QUOTES, 'UTF-8') ?>)</summary>
                                    <?php if (!empty($comentarios)): ?>
                                        <ul class="list-group list-group-flush mt-2">
                                            <?php foreach ($comentarios as $comentario): ?>
                                                <li class="list-group-item px-0">
                                                    <p class="mb-1 small">
                                                        <strong><?= htmlspecialchars((string) ($comentario->getNomeUsuario() ?: 'Usuário'), ENT_QUOTES, 'UTF-8') ?>:</strong>
                                                        <?= htmlspecialchars((string) $comentario->getTexto(), ENT_QUOTES, 'UTF-8') ?>
                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="small text-muted">
                                                            <?= htmlspecialchars((string) $comentario->getDataComentario(), ENT_QUOTES, 'UTF-8') ?>
                                                        </span>
                                                        <form action="index.php?rota=processar_exclusao_comentario_admin" method="POST"
                                                            onsubmit="return confirm('Remover este comentário?');">
                                                            <?= csrfField() ?>
                                                            <input type="hidden" name="id_comentario"
                                                                value="<?= htmlspecialchars((string) $comentario->getId(), ENT_QUOTES, 'UTF-8') ?>">
                                                            <input type="hidden" name="retorno_filtros"
                                                                value="<?= htmlspecialchars((string) $queryFiltrosComPaginaAtual, ENT_QUOTES, 'UTF-8') ?>">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm">Remover</button>
                                                        </form>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="small text-muted mb-0 mt-2">Sem comentários nesta denúncia.</p>
                                    <?php endif; ?>
                                </details>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPaginas > 1): ?>
            <nav aria-label="Paginação de denúncias" class="mt-3">
                <ul class="pagination mb-0">
                    <?php
                    $queryBase = 'index.php?rota=admin_denuncias';
                    if ($queryFiltrosSemPagina !== '') {
                        $queryBase .= '&' . $queryFiltrosSemPagina;
                    }
                    ?>
                    <?php for ($pagina = 1; $pagina <= $totalPaginas; $pagina++): ?>
                        <li class="page-item <?= $pagina === (int) $paginaAtual ? 'active' : '' ?>">
                            <a class="page-link"
                                href="<?= htmlspecialchars($queryBase . '&pagina=' . $pagina, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars((string) $pagina, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-info mb-0" role="alert">
            Nenhuma denúncia encontrada para os filtros selecionados.
        </div>
    <?php endif; ?>
</section>

<?php include "view/templates/footer.php"; ?>