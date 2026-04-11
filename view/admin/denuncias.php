<?php include "view/templates/header.php"; ?>

<div class="container py-4 painel-shell">
    <section class="painel-conteudo-col">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
                <h2 class="mb-1">Gerenciamento de Denúncias</h2>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-3 gap-2 flex-wrap">
            <button type="button"
                class="btn btn-outline-secondary btn-sm rounded-pill d-inline-flex align-items-center gap-2"
                data-bs-toggle="collapse" data-bs-target="#adminDenunciasFiltrosCard"
                aria-controls="adminDenunciasFiltrosCard" aria-expanded="false">
                <img src="assets/fonts/material-symbols/filter_alt.svg" alt="filtro" class="nav-icon" width="16"
                    height="16">
                Filtrar
            </button>
        </div>

        <div class="collapse mb-3" id="adminDenunciasFiltrosCard">
            <div class="card shadow-sm painel-filtros-card w-100">
                <div class="card-body">
                    <h4 class="h6 mb-3">Filtros</h4>
                    <form action="index.php" method="GET" class="painel-filtros-form">
                        <input type="hidden" name="rota" value="admin_denuncias">

                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-3">
                                <label for="filtroStatus" class="form-label">Status</label>
                                <select id="filtroStatus" name="status" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    <option value="Aberto" <?= ($filtrosAtuais['status'] ?? '') === 'Aberto' ? 'selected' : '' ?>>
                                        Aberto</option>
                                    <option value="Em Andamento" <?= ($filtrosAtuais['status'] ?? '') === 'Em Andamento' ? 'selected' : '' ?>>Em Andamento</option>
                                    <option value="Resolvido" <?= ($filtrosAtuais['status'] ?? '') === 'Resolvido' ? 'selected' : '' ?>>Resolvido</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-3">
                                <label for="filtroCategoria" class="form-label">Categoria</label>
                                <select id="filtroCategoria" name="categoria" class="form-select form-select-sm">
                                    <option value="">Todas</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option
                                            value="<?= htmlspecialchars((string) $categoria->getId(), ENT_QUOTES, 'UTF-8') ?>"
                                            <?= (int) ($filtrosAtuais['categoria'] ?? 0) === (int) $categoria->getId() ? 'selected' : '' ?>>
                                            <?= htmlspecialchars((string) $categoria->getNomeCategoria(), ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-3">
                                <label for="filtroBusca" class="form-label">Busca</label>
                                <input type="text" id="filtroBusca" name="busca" class="form-control form-control-sm"
                                    value="<?= htmlspecialchars((string) ($filtrosAtuais['busca'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    maxlength="120" placeholder="Título, descrição ou local">
                            </div>

                            <div class="col-6 col-md-1">
                                <label for="filtroLimite" class="form-label">Limite</label>
                                <select id="filtroLimite" name="limite" class="form-select form-select-sm">
                                    <?php foreach ([10, 20, 50] as $opcaoLimite): ?>
                                        <option value="<?= $opcaoLimite ?>" <?= (int) ($filtrosAtuais['limite'] ?? 10) === $opcaoLimite ? 'selected' : '' ?>>
                                            <?= $opcaoLimite ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-6 col-md-2">
                                <label for="filtroOrdem" class="form-label">Ordem</label>
                                <select id="filtroOrdem" name="ordem" class="form-select form-select-sm">
                                    <option value="recentes" <?= ($filtrosAtuais['ordem'] ?? 'recentes') === 'recentes' ? 'selected' : '' ?>>Mais recentes</option>
                                    <option value="antigas" <?= ($filtrosAtuais['ordem'] ?? '') === 'antigas' ? 'selected' : '' ?>>Mais antigas</option>
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2 mt-1">
                                <a href="index.php?rota=admin_denuncias"
                                    class="btn btn-outline-secondary btn-sm">Limpar</a>
                                <button type="submit" class="btn btn-primary btn-sm">Aplicar filtros</button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-3 text-muted small">
                        Total de denúncias encontradas:
                        <strong><?= htmlspecialchars((string) $totalDenuncias, ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <section class="mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h3 class="mb-0">Denuncias registradas</h3>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small">Total encontrado:
                        <strong><?= htmlspecialchars((string) $totalDenuncias, ENT_QUOTES, 'UTF-8') ?></strong></span>
                    <?php if ($totalPaginas > 1): ?>
                        <span class="text-muted small">Pagina
                            <?= htmlspecialchars((string) $paginaAtual, ENT_QUOTES, 'UTF-8') ?> de
                            <?= htmlspecialchars((string) $totalPaginas, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($denuncias)): ?>
                <div class="painel-denuncias-lista mb-3">
                    <?php foreach ($denuncias as $denuncia): ?>
                        <?php
                        $idDenuncia = (int) $denuncia->getId();
                        $idUsuario = (int) $denuncia->getIdUsuario();
                        $idCategoria = (int) $denuncia->getIdCategoria();
                        $nomeUsuario = $mapaUsuarios[$idUsuario] ?? 'Usuário não identificado';
                        $nomeCategoria = $mapaCategorias[$idCategoria] ?? 'Categoria indisponível';
                        $comentarios = $interacoesDenuncias[$idDenuncia] ?? [];
                        $dataCriacaoRaw = (string) $denuncia->getDataCriacao();
                        $timestampDataCriacao = strtotime($dataCriacaoRaw);
                        $dataCriacaoFormatada = $timestampDataCriacao !== false ? date('d/m/Y H:i', $timestampDataCriacao) : $dataCriacaoRaw;
                        $fotoPath = method_exists($denuncia, 'getFotoPath') ? (string) $denuncia->getFotoPath() : '';
                        ?>
                        <article class="card shadow-sm painel-denuncia-card">
                            <div class="painel-denuncia-thumb-wrap">
                                <?php if ($fotoPath !== ''): ?>
                                    <img src="<?= htmlspecialchars($fotoPath, ENT_QUOTES, 'UTF-8') ?>" alt="Foto da denúncia"
                                        class="painel-denuncia-thumb">
                                <?php else: ?>
                                    <div class="painel-denuncia-thumb painel-denuncia-thumb-placeholder" aria-hidden="true"></div>
                                <?php endif; ?>
                            </div>

                            <div class="card-body d-flex flex-column painel-denuncia-conteudo">
                                <div
                                    class="d-flex justify-content-between align-items-start gap-2 mb-2 flex-wrap painel-denuncia-cabecalho border-0">
                                    <h4 class="card-title mb-0 painel-denuncia-titulo">
                                        <?= htmlspecialchars((string) $denuncia->getTitulo(), ENT_QUOTES, 'UTF-8') ?>
                                    </h4>
                                    <div class="d-flex gap-2 align-items-center flex-wrap">
                                        <span
                                            class="badge bg-transparent border border-secondary-subtle text-body"><?= htmlspecialchars((string) $nomeCategoria, ENT_QUOTES, 'UTF-8') ?></span>
                                        <span
                                            class="badge bg-transparent border border-info-subtle text-info-emphasis"><?= htmlspecialchars((string) $denuncia->getStatus(), ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </div>

                                <p class="mb-1 painel-denuncia-meta"><strong>Localização:</strong>
                                    <?= htmlspecialchars((string) $denuncia->getLocalizacao(), ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="mb-1 painel-denuncia-meta"><strong>Autor:</strong>
                                    <?= htmlspecialchars((string) $nomeUsuario, ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="mb-2 painel-denuncia-meta"><strong>Criada em:</strong>
                                    <?= htmlspecialchars((string) $dataCriacaoFormatada, ENT_QUOTES, 'UTF-8') ?></p>

                                <p class="mb-3 painel-denuncia-resumo">
                                    <?= htmlspecialchars(mb_strimwidth((string) $denuncia->getDescricao(), 0, 200, '...'), ENT_QUOTES, 'UTF-8') ?>
                                </p>

                                <div class="mt-auto pt-2 border-top painel-denuncia-rodape">
                                    <form action="index.php?rota=processar_status_denuncia_admin" method="POST"
                                        class="d-flex flex-wrap gap-2 mb-2">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id_denuncia"
                                            value="<?= htmlspecialchars((string) $idDenuncia, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="retorno_filtros"
                                            value="<?= htmlspecialchars((string) $queryFiltrosComPaginaAtual, ENT_QUOTES, 'UTF-8') ?>">

                                        <select name="status" class="form-select form-select-sm" style="min-width: 140px;"
                                            required>
                                            <option value="Aberto" <?= $denuncia->getStatus() === 'Aberto' ? 'selected' : '' ?>>
                                                Aberto</option>
                                            <option value="Em Andamento" <?= $denuncia->getStatus() === 'Em Andamento' ? 'selected' : '' ?>>Em Andamento</option>
                                            <option value="Resolvido" <?= $denuncia->getStatus() === 'Resolvido' ? 'selected' : '' ?>>Resolvido</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline-primary btn-sm">Salvar Status</button>
                                    </form>

                                    <details>
                                        <summary class="small">Comentários
                                            (<?= htmlspecialchars((string) count($comentarios), ENT_QUOTES, 'UTF-8') ?>)
                                        </summary>
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
                                                            <form action="index.php?rota=processar_exclusao_comentario_admin"
                                                                method="POST" onsubmit="return confirm('Remover este comentário?');">
                                                                <?= csrfField() ?>
                                                                <input type="hidden" name="id_comentario"
                                                                    value="<?= htmlspecialchars((string) $comentario->getId(), ENT_QUOTES, 'UTF-8') ?>">
                                                                <input type="hidden" name="retorno_filtros"
                                                                    value="<?= htmlspecialchars((string) $queryFiltrosComPaginaAtual, ENT_QUOTES, 'UTF-8') ?>">
                                                                <button type="submit"
                                                                    class="btn btn-outline-danger btn-sm">Remover</button>
                                                            </form>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="small text-muted mb-0 mt-2">Sem comentários nesta denúncia.</p>
                                        <?php endif; ?>
                                    </details>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPaginas > 1): ?>
                    <div class="d-flex flex-column gap-3 mt-4">
                        <nav class="painel-paginacao-nav" aria-label="Paginacao das denuncias">
                            <ul class="pagination painel-paginacao flex-wrap mb-0">
                                <?php
                                $baseQuery = 'rota=admin_denuncias';
                                if ($queryFiltrosSemPagina !== '') {
                                    $baseQuery .= '&' . $queryFiltrosSemPagina;
                                }
                                ?>

                                <?php if ((int) $paginaAtual > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=' . ((int) $paginaAtual - 1), ENT_QUOTES, 'UTF-8') ?>">Anterior</a>
                                    </li>
                                <?php endif; ?>

                                <?php
                                $inicioPaginacao = max(1, (int) $paginaAtual - 2);
                                $fimPaginacao = min((int) $totalPaginas, (int) $paginaAtual + 2);
                                ?>

                                <?php if ($inicioPaginacao > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=1', ENT_QUOTES, 'UTF-8') ?>">1</a>
                                    </li>
                                    <?php if ($inicioPaginacao > 2): ?>
                                        <li class="page-item disabled painel-paginacao-ellipsis"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($pagina = $inicioPaginacao; $pagina <= $fimPaginacao; $pagina++): ?>
                                    <li class="page-item <?= ((int) $pagina === (int) $paginaAtual) ? 'active' : '' ?>">
                                        <a class="page-link"
                                            href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=' . $pagina, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $pagina, ENT_QUOTES, 'UTF-8') ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($fimPaginacao < (int) $totalPaginas): ?>
                                    <?php if ($fimPaginacao < (int) $totalPaginas - 1): ?>
                                        <li class="page-item disabled painel-paginacao-ellipsis"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=' . (int) $totalPaginas, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $totalPaginas, ENT_QUOTES, 'UTF-8') ?></a>
                                    </li>
                                <?php endif; ?>

                                <?php if ((int) $paginaAtual < (int) $totalPaginas): ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=' . ((int) $paginaAtual + 1), ENT_QUOTES, 'UTF-8') ?>">Proxima</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info mb-0" role="alert">
                    Nenhuma denúncia encontrada para os filtros selecionados.
                </div>
            <?php endif; ?>
        </section>
    </section>
</div>

<?php include "view/templates/footer.php"; ?>