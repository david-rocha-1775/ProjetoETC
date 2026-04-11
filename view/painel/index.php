<?php
// View do Painel do Usuario (Dashboard)
?>
<?php include "view/templates/header.php"; ?>
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

$denuncias = isset($denuncias) && is_array($denuncias) ? $denuncias : [];
$totalNaPagina = count($denuncias);
$statusEmAnalise = isset($totalStatusEmAnalise) ? (int) $totalStatusEmAnalise : 0;
$statusResolvido = isset($totalStatusResolvido) ? (int) $totalStatusResolvido : 0;

if (!isset($totalStatusEmAnalise) || !isset($totalStatusResolvido)) {
    foreach ($denuncias as $denunciaMetrica) {
        $statusNormalizado = mb_strtolower(trim((string) $denunciaMetrica->getStatus()));

        if (str_contains($statusNormalizado, 'resolvido')) {
            $statusResolvido++;
        } else {
            $statusEmAnalise++;
        }
    }
}
?>

<div class="container py-4 painel-shell">
    <div id="painel-feedback" class="mb-3" aria-live="polite"></div>

    <section class="painel-conteudo-col">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
                <h2 class="mb-1">Início</h2>
                <p class="text-muted mb-0">Acompanhe o status das suas solicitacoes.</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <article class="card shadow-sm h-100 painel-metrica-card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small text-muted mb-1">Total de relatos</p>
                            <p class="display-6 mb-0 fw-semibold"><?= htmlspecialchars((string) $totalDenuncias) ?>
                            </p>
                        </div>
                        <img src="assets/fonts/material-symbols/analytics4.svg" alt="total" width="24" height="24"
                            class="painel-metrica-icone">
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="card shadow-sm h-100 painel-metrica-card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small text-muted mb-1">Em analise</p>
                            <p class="display-6 mb-0 fw-semibold"><?= htmlspecialchars((string) $statusEmAnalise) ?>
                            </p>
                            <span class="small text-muted">total</span>
                        </div>
                        <img src="assets/fonts/material-symbols/pending.svg" alt="em analise" width="24" height="24"
                            class="painel-metrica-icone">
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="card shadow-sm h-100 painel-metrica-card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small text-muted mb-1">Resolvidos</p>
                            <p class="display-6 mb-0 fw-semibold"><?= htmlspecialchars((string) $statusResolvido) ?>
                            </p>
                            <span class="small text-muted">total</span>
                        </div>
                        <img src="assets/fonts/material-symbols/task_alt_.svg" alt="resolvidos" width="24" height="24"
                            class="painel-metrica-icone">
                    </div>
                </article>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-3 gap-2 flex-wrap">
            <button type="button"
                class="btn btn-outline-secondary btn-sm rounded-pill d-inline-flex align-items-center gap-2"
                data-bs-toggle="collapse" data-bs-target="#painelFiltrosCard" aria-controls="painelFiltrosCard"
                aria-expanded="false">
                <img src="assets/fonts/material-symbols/filter_alt.svg" alt="filtro" class="nav-icon" width="16"
                    height="16">
                Filtrar
            </button>
            <a href="index.php?rota=nova_denuncia"
                class="btn btn-primary btn-sm rounded-pill d-inline-flex align-items-center gap-2 painel-btn-nova-denuncia">
                <img src="assets/fonts/material-symbols/add_circle.svg" alt="nova denuncia" class="nav-icon" width="16"
                    height="16">
                Nova denuncia
            </a>
        </div>

        <div class="collapse mb-3" id="painelFiltrosCard">
            <div class="card shadow-sm painel-filtros-card w-100">
                <div class="card-body">
                    <h4 class="h6 mb-3">Filtros</h4>
                    <form method="GET" action="index.php" class="painel-filtros-form">
                        <input type="hidden" name="rota" value="painel">

                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <label for="filtro-categoria-card" class="form-label mb-0 small">Categoria</label>
                                <select name="categoria" id="filtro-categoria-card" class="form-select form-select-sm">
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

                            <div class="col-6 col-md-3">
                                <label for="filtro-limite-card" class="form-label mb-0 small">Itens por
                                    pagina</label>
                                <select name="limite" id="filtro-limite-card" class="form-select form-select-sm">
                                    <option value="10" <?= ($limiteSelecionado === 10) ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= ($limiteSelecionado === 25) ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= ($limiteSelecionado === 50) ? 'selected' : '' ?>>50</option>
                                </select>
                            </div>

                            <div class="col-6 col-md-3">
                                <label for="filtro-ordem-card" class="form-label mb-0 small">Ordenacao</label>
                                <select name="ordem" id="filtro-ordem-card" class="form-select form-select-sm">
                                    <option value="recentes" <?= ($ordenacaoSelecionada === 'recentes') ? 'selected' : '' ?>>Mais recentes</option>
                                    <option value="antigas" <?= ($ordenacaoSelecionada === 'antigas') ? 'selected' : '' ?>>
                                        Mais antigas</option>
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2 mt-1">
                                <a href="index.php?rota=painel" class="btn btn-outline-secondary btn-sm">Limpar</a>
                                <button type="submit" class="btn btn-primary btn-sm">Aplicar filtros</button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-3 text-muted small">
                        Total de denuncias encontradas:
                        <strong><?= htmlspecialchars((string) $totalDenuncias) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <section class="mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h3 class="mb-0">Denuncias registradas</h3>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small">Total encontrado:
                        <strong><?= htmlspecialchars((string) $totalDenuncias) ?></strong></span>
                    <?php if ($totalPaginas > 1): ?>
                        <span class="text-muted small">Pagina <?= htmlspecialchars((string) $paginaAtual) ?> de
                            <?= htmlspecialchars((string) $totalPaginas) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($totalNaPagina > 0): ?>
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
                        $dataCriacaoRaw = (string) $d->getDataCriacao();
                        $timestampDataCriacao = strtotime($dataCriacaoRaw);
                        $dataCriacaoFormatada = $timestampDataCriacao !== false
                            ? date('d/m/y', $timestampDataCriacao)
                            : $dataCriacaoRaw;
                        ?>
                        <article id="denuncia-<?= htmlspecialchars((string) $idDenuncia) ?>"
                            data-denuncia-id="<?= htmlspecialchars((string) $idDenuncia) ?>"
                            class="card shadow-sm painel-denuncia-card">
                            <div class="painel-denuncia-thumb-wrap">
                                <?php if ($d->getFotoPath()): ?>
                                    <img src="<?= htmlspecialchars((string) $d->getFotoPath()) ?>" alt="Foto da denuncia"
                                        class="painel-denuncia-thumb">
                                <?php else: ?>
                                    <div class="painel-denuncia-thumb painel-denuncia-thumb-placeholder" aria-hidden="true">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-body d-flex flex-column painel-denuncia-conteudo">
                                <div
                                    class="d-flex justify-content-between align-items-start gap-2 mb-2 flex-wrap painel-denuncia-cabecalho">
                                    <h4 class="card-title mb-0 painel-denuncia-titulo">
                                        <?= htmlspecialchars($d->getTitulo()) ?>
                                    </h4>
                                    <div class="d-flex gap-2 align-items-center flex-wrap">
                                        <span class="badge text-bg-secondary"><?= htmlspecialchars($nomeCategoria) ?></span>
                                        <span class="badge text-bg-dark"><?= htmlspecialchars($d->getStatus()) ?></span>
                                    </div>
                                </div>

                                <p class="mb-1 painel-denuncia-meta"><strong>Localizacao:</strong>
                                    <?= htmlspecialchars($d->getLocalizacao()) ?></p>
                                <p class="mb-1 painel-denuncia-meta"><strong>Autor:</strong>
                                    <?= htmlspecialchars($nomeAutor) ?></p>
                                <p class="mb-2 painel-denuncia-meta"><strong>Criada em:</strong>
                                    <?= htmlspecialchars($dataCriacaoFormatada) ?></p>
                                <p class="mb-3 painel-denuncia-resumo">
                                    <?= htmlspecialchars(mb_strimwidth((string) $d->getDescricao(), 0, 180, '...')) ?>
                                </p>

                                <div
                                    class="d-flex align-items-center flex-wrap gap-2 mt-auto pt-2 border-top painel-denuncia-rodape">
                                    <form
                                        action="index.php?<?= htmlspecialchars($queryFiltrosPainel . ($queryFiltrosPainel !== '' ? '&' : '') . 'rota=processar_curtida_denuncia') ?>"
                                        method="POST" class="js-curtir-denuncia mb-0">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id_denuncia"
                                            value="<?= htmlspecialchars((string) $idDenuncia) ?>">
                                        <button type="submit"
                                            class="p-0 border-0 bg-transparent shadow-none painel-icone-curtir <?= $resumoDenuncia['usuarioCurtiu'] ? 'is-active' : '' ?>"
                                            data-botao-curtir-denuncia data-curtir-icone="1"
                                            data-curtir-icone-off="assets/fonts/material-symbols/thumb.svg"
                                            data-curtir-icone-on="assets/fonts/material-symbols/thumb_up.svg"
                                            aria-label="<?= $resumoDenuncia['usuarioCurtiu'] ? 'Remover curtida' : 'Curtir' ?>"
                                            title="<?= $resumoDenuncia['usuarioCurtiu'] ? 'Remover curtida' : 'Curtir' ?>">
                                            <img src="<?= $resumoDenuncia['usuarioCurtiu'] ? 'assets/fonts/material-symbols/thumb_up.svg' : 'assets/fonts/material-symbols/thumb.svg' ?>"
                                                alt="curtir" class="nav-icon" width="18" height="18">
                                        </button>
                                    </form>
                                    <div class="painel-denuncia-indicadores small">
                                        <span class="painel-indicador-curtidas d-inline-flex align-items-center gap-1">
                                            <strong
                                                id="total-curtidas-denuncia-<?= htmlspecialchars((string) $idDenuncia) ?>"><?= htmlspecialchars((string) $resumoDenuncia['totalCurtidas']) ?></strong></span>
                                        <span class="painel-indicador-comentarios d-inline-flex align-items-center gap-1">
                                            <img src="assets/fonts/material-symbols/chat_bubble.svg" alt="comentarios"
                                                class="nav-icon" width="18" height="18">
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
                        <nav class="painel-paginacao-nav" aria-label="Paginacao das denuncias">
                            <ul class="pagination painel-paginacao flex-wrap mb-0">
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
                                        <a class="page-link"
                                            href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=1') ?>">1</a>
                                    </li>
                                    <?php if ($inicioPaginacao > 2): ?>
                                        <li class="page-item disabled painel-paginacao-ellipsis"><span class="page-link">...</span></li>
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
                                        <li class="page-item disabled painel-paginacao-ellipsis"><span class="page-link">...</span></li>
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