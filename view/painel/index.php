<?php
// View do Painel do Usuario (Dashboard)
?>
<?php include "view/templates/header.php"; ?>
<div class="container py-4">
    <div id="painel-feedback" class="mb-3" aria-live="polite"></div>

    <section class="mb-4">
        <h2 class="mb-1">Denuncie problemas no seu bairro</h2>
        <p class="text-muted mb-3">Ajude a prefeitura a identificar buracos, falta de iluminacao e mais.</p>

        <a href="index.php?rota=nova_denuncia" class="btn btn-primary">Fazer uma Denuncia</a>
    </section>

    <?php
    $categoriaSelecionada = isset($filtroCategoriaSelecionada) ? (int) $filtroCategoriaSelecionada : 0;
    $limiteSelecionado = isset($filtroLimiteSelecionado) ? (int) $filtroLimiteSelecionado : 10;
    $ordenacaoSelecionada = isset($filtroOrdenacaoSelecionada) ? (string) $filtroOrdenacaoSelecionada : 'recentes';
    $paginaAtual = isset($paginaAtual) ? (int) $paginaAtual : 1;
    $totalPaginas = isset($totalPaginas) ? (int) $totalPaginas : 0;
    $totalDenuncias = isset($totalDenuncias) ? (int) $totalDenuncias : 0;
    $queryFiltrosPainel = isset($painelQueryFiltros) ? (string) $painelQueryFiltros : '';

    $baseParams = [
        'rota' => 'painel',
        'limite' => $limiteSelecionado,
    ];

    if ($categoriaSelecionada > 0) {
        $baseParams['categoria'] = $categoriaSelecionada;
    }

    if ($ordenacaoSelecionada !== 'recentes') {
        $baseParams['ordem'] = $ordenacaoSelecionada;
    }

    $baseQuery = http_build_query($baseParams);
    ?>

    <section class="mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title mb-3">Filtrar denuncias</h3>

                <form method="GET" action="index.php" class="row g-3 align-items-end">
                    <input type="hidden" name="rota" value="painel">

                    <div class="col-12 col-md-5">
                        <label for="filtro-categoria" class="form-label">Categoria</label>
                        <select name="categoria" id="filtro-categoria" class="form-select">
                            <option value="">Todas as categorias</option>
                            <?php if (isset($categorias) && is_array($categorias)): ?>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= htmlspecialchars($categoria->getId()) ?>"
                                        <?= ($categoriaSelecionada === (int) $categoria->getId()) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categoria->getNomeCategoria()) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="filtro-limite" class="form-label">Itens por pagina</label>
                        <select name="limite" id="filtro-limite" class="form-select">
                            <option value="10" <?= ($limiteSelecionado === 10) ? 'selected' : '' ?>>10</option>
                            <option value="25" <?= ($limiteSelecionado === 25) ? 'selected' : '' ?>>25</option>
                            <option value="50" <?= ($limiteSelecionado === 50) ? 'selected' : '' ?>>50</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="filtro-ordem" class="form-label">Ordenacao</label>
                        <select name="ordem" id="filtro-ordem" class="form-select">
                            <option value="recentes" <?= ($ordenacaoSelecionada === 'recentes') ? 'selected' : '' ?>>Mais
                                recentes</option>
                            <option value="antigas" <?= ($ordenacaoSelecionada === 'antigas') ? 'selected' : '' ?>>Mais
                                antigas</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                        <a href="index.php?rota=painel" class="btn btn-outline-secondary">Limpar</a>
                    </div>
                </form>

                <div class="mt-3 text-muted small">
                    Total de denuncias encontradas: <strong><?= htmlspecialchars((string) $totalDenuncias) ?></strong>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h3 class="mb-0">Denuncias registradas</h3>

            <?php if ($totalPaginas > 1): ?>
                <span class="text-muted">Pagina <?= htmlspecialchars((string) $paginaAtual) ?> de
                    <?= htmlspecialchars((string) $totalPaginas) ?></span>
            <?php endif; ?>
        </div>

        <?php if (count($denuncias) > 0): ?>
            <div class="painel-denuncias-lista mb-3">
                <?php foreach ($denuncias as $d): ?>
                    <?php
                    $idDenuncia = (int) $d->getId();
                    $resumoDenuncia = $resumoInteracoes[$idDenuncia] ?? [
                        'totalCurtidas' => 0,
                        'totalComentarios' => 0,
                        'usuarioCurtiu' => false,
                    ];
                    $nomeAutor = $autoresPorDenuncia[$idDenuncia] ?? 'Usuario';
                    $nomeCategoria = $categoriasPorId[(int) $d->getIdCategoria()] ?? 'Sem categoria';
                    $urlDetalhe = 'index.php?rota=detalhe_denuncia&id=' . rawurlencode((string) $idDenuncia);
                    ?>
                    <article id="denuncia-<?= htmlspecialchars((string) $idDenuncia) ?>"
                        data-denuncia-id="<?= htmlspecialchars((string) $idDenuncia) ?>"
                        class="card shadow-sm painel-denuncia-card">
                        <div class="painel-denuncia-thumb-wrap">
                            <?php if ($d->getFotoPath()): ?>
                                <img src="<?= htmlspecialchars((string) $d->getFotoPath()) ?>" alt="Foto da denuncia"
                                    class="painel-denuncia-thumb">
                            <?php else: ?>
                                <div class="painel-denuncia-thumb painel-denuncia-thumb-placeholder" aria-hidden="true"></div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column painel-denuncia-conteudo">
                            <div
                                class="d-flex justify-content-between align-items-start gap-2 mb-2 flex-wrap painel-denuncia-cabecalho">
                                <h4 class="card-title mb-0 painel-denuncia-titulo"><?= htmlspecialchars($d->getTitulo()) ?></h4>
                                <div class="d-flex gap-2 align-items-center flex-wrap">
                                    <span class="badge text-bg-secondary"><?= htmlspecialchars($nomeCategoria) ?></span>
                                    <span class="badge text-bg-dark"><?= htmlspecialchars($d->getStatus()) ?></span>
                                </div>
                            </div>

                            <p class="mb-1 painel-denuncia-meta"><strong>Localizacao:</strong>
                                <?= htmlspecialchars($d->getLocalizacao()) ?></p>
                            <p class="mb-1 painel-denuncia-meta"><strong>Autor:</strong> <?= htmlspecialchars($nomeAutor) ?></p>
                            <p class="mb-2 painel-denuncia-meta"><strong>Criada em:</strong>
                                <?= htmlspecialchars((string) $d->getDataCriacao()) ?></p>
                            <p class="mb-3 painel-denuncia-resumo">
                                <?= htmlspecialchars(mb_strimwidth((string) $d->getDescricao(), 0, 180, '...')) ?></p>

                            <div
                                class="d-flex align-items-center flex-wrap gap-2 mt-auto pt-2 border-top painel-denuncia-rodape">
                                <form
                                    action="index.php?<?= htmlspecialchars($queryFiltrosPainel . ($queryFiltrosPainel !== '' ? '&' : '') . 'rota=processar_curtida_denuncia') ?>"
                                    method="POST" class="js-curtir-denuncia mb-0">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id_denuncia"
                                        value="<?= htmlspecialchars((string) $idDenuncia) ?>">
                                    <button type="submit" class="btn btn-outline-primary btn-sm" data-botao-curtir-denuncia>
                                        <?= $resumoDenuncia['usuarioCurtiu'] ? 'Descurtir' : 'Curtir' ?>
                                    </button>
                                </form>
                                <div class="painel-denuncia-indicadores small">
                                    <span class="painel-indicador-curtidas">Curtidas:
                                        <strong
                                            id="total-curtidas-denuncia-<?= htmlspecialchars((string) $idDenuncia) ?>"><?= htmlspecialchars((string) $resumoDenuncia['totalCurtidas']) ?></strong></span>
                                    <span class="painel-indicador-comentarios">Comentarios:
                                        <strong><?= htmlspecialchars((string) $resumoDenuncia['totalComentarios']) ?></strong></span>
                                </div>
                                <a href="<?= htmlspecialchars($urlDetalhe) ?>" class="btn btn-primary btn-sm ms-md-auto">Ver
                                    detalhes</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPaginas > 1): ?>
                <div class="d-flex flex-column gap-3 mt-4">
                    <?php if ($paginaAtual < $totalPaginas): ?>
                        <div>
                            <a class="btn btn-success"
                                href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=' . ($paginaAtual + 1)) ?>">Ver mais
                                denuncias</a>
                        </div>
                    <?php endif; ?>

                    <nav aria-label="Paginacao das denuncias">
                        <ul class="pagination flex-wrap mb-0">
                            <?php if ($paginaAtual > 1): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=' . ($paginaAtual - 1)) ?>">Anterior</a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $inicioPaginacao = max(1, $paginaAtual - 2);
                            $fimPaginacao = min($totalPaginas, $paginaAtual + 2);
                            ?>

                            <?php if ($inicioPaginacao > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=1') ?>">1</a>
                                </li>
                                <?php if ($inicioPaginacao > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($pagina = $inicioPaginacao; $pagina <= $fimPaginacao; $pagina++): ?>
                                <li class="page-item <?= ($pagina === $paginaAtual) ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=' . $pagina) ?>"><?= htmlspecialchars((string) $pagina) ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($fimPaginacao < $totalPaginas): ?>
                                <?php if ($fimPaginacao < $totalPaginas - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=' . $totalPaginas) ?>"><?= htmlspecialchars((string) $totalPaginas) ?></a>
                                </li>
                            <?php endif; ?>

                            <?php if ($paginaAtual < $totalPaginas): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=' . ($paginaAtual + 1)) ?>">Proxima</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">Nenhuma denuncia registrada ainda. Seja o primeiro!</div>
        <?php endif; ?>
    </section>
</div>

<?php include "view/templates/footer.php"; ?>