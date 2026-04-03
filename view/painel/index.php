<?php
// View do Painel do Usuário (Dashboard)
$paginaCssExtra = [
    'assets/vendor/leaflet/leaflet.css',
];
$paginaJsHeadExtra = [
    'assets/vendor/leaflet/leaflet.js',
    'assets/js/leaflet-offline.js',
];
?>
<?php include "view/templates/header.php"; ?>
<div class="container py-4">
    <div id="painel-feedback" class="mb-3" aria-live="polite"></div>

    <section class="mb-4">
        <h2 class="mb-1">Denuncie problemas no seu bairro</h2>
        <p class="text-muted mb-3">Ajude a prefeitura a identificar buracos, falta de iluminação e mais.</p>

        <a href="index.php?rota=nova_denuncia" class="btn btn-primary">Fazer uma Denúncia</a>
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
                <h3 class="card-title mb-3">Filtrar denúncias</h3>

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
                        <label for="filtro-limite" class="form-label">Itens por página</label>
                        <select name="limite" id="filtro-limite" class="form-select">
                            <option value="10" <?= ($limiteSelecionado === 10) ? 'selected' : '' ?>>10</option>
                            <option value="25" <?= ($limiteSelecionado === 25) ? 'selected' : '' ?>>25</option>
                            <option value="50" <?= ($limiteSelecionado === 50) ? 'selected' : '' ?>>50</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="filtro-ordem" class="form-label">Ordenação</label>
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
                    Total de denúncias encontradas: <strong><?= htmlspecialchars((string) $totalDenuncias) ?></strong>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h3 class="mb-0">Denúncias registradas</h3>

            <?php if ($totalPaginas > 1): ?>
                <span class="text-muted">Página <?= htmlspecialchars((string) $paginaAtual) ?> de
                    <?= htmlspecialchars((string) $totalPaginas) ?></span>
            <?php endif; ?>
        </div>

        <?php if (count($denuncias) > 0): ?>
            <?php foreach ($denuncias as $d): ?>
                <div id="denuncia-<?= htmlspecialchars($d->getId()) ?>" data-denuncia-id="<?= htmlspecialchars($d->getId()) ?>"
                    class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h4 class="card-title mb-3"><?= htmlspecialchars($d->getTitulo()) ?></h4>

                        <p class="mb-1"><strong>Localização:</strong> <?= htmlspecialchars($d->getLocalizacao()) ?></p>

                        <p class="mb-2"><strong>Status:</strong> <?= htmlspecialchars($d->getStatus()) ?></p>

                        <?php
                        $interacaoDenuncia = $interacoes[(int) $d->getId()] ?? [
                            'comentarios' => [],
                            'totalCurtidas' => 0,
                            'usuarioCurtiu' => false,
                        ];
                        ?>

                        <div class="d-inline-flex align-items-center gap-2 mb-3 mt-1">
                            <form
                                action="index.php?<?= htmlspecialchars($queryFiltrosPainel . ($queryFiltrosPainel !== '' ? '&' : '') . 'rota=processar_curtida_denuncia') ?>"
                                method="POST" class="js-curtir-denuncia">
                                <input type="hidden" name="id_denuncia" value="<?= htmlspecialchars($d->getId()) ?>">
                                <button type="submit" class="btn btn-outline-primary btn-sm" data-botao-curtir-denuncia>
                                    <?= $interacaoDenuncia['usuarioCurtiu'] ? 'Descurtir' : 'Curtir' ?>
                                </button>
                            </form>
                            <span>Curtidas:
                                <strong
                                    id="total-curtidas-denuncia-<?= htmlspecialchars($d->getId()) ?>"><?= htmlspecialchars($interacaoDenuncia['totalCurtidas']) ?></strong></span>
                        </div>

                        <?php if ($d->getFotoPath()): ?>

                            <img src="<?= htmlspecialchars($d->getFotoPath()) ?>" alt="Foto da denúncia"
                                class="img-fluid rounded border mb-3" style="max-width: 300px; max-height: 200px;">

                        <?php endif; ?>

                        <?php
                        $usuarioLogadoId = (int) ($_SESSION['usuario_id'] ?? 0);
                        $usuarioAdmin = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';
                        $podeGerenciar = $usuarioAdmin || $usuarioLogadoId === (int) $d->getIdUsuario();
                        ?>

                        <?php if ($podeGerenciar): ?>
                            <details class="mt-3">
                                <summary><strong>Editar denúncia</strong></summary>
                                <form
                                    action="index.php?<?= htmlspecialchars($queryFiltrosPainel . ($queryFiltrosPainel !== '' ? '&' : '') . 'rota=processar_edicao_denuncia') ?>"
                                    method="POST" enctype="multipart/form-data" class="mt-3">
                                    <input type="hidden" name="id_denuncia" value="<?= htmlspecialchars($d->getId()) ?>">

                                    <div class="mb-3">
                                        <label class="form-label">Título</label>
                                        <input type="text" name="titulo" class="form-control"
                                            value="<?= htmlspecialchars($d->getTitulo()) ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Descrição</label>
                                        <textarea name="descricao" rows="3" class="form-control"
                                            required><?= htmlspecialchars($d->getDescricao()) ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Localização</label>
                                        <input type="text" name="localizacao" class="form-control"
                                            value="<?= htmlspecialchars($d->getLocalizacao()) ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Categoria</label>
                                        <select name="id_categoria" class="form-select" required>
                                            <?php if (isset($categorias) && is_array($categorias)): ?>
                                                <?php foreach ($categorias as $categoria): ?>
                                                    <option value="<?= htmlspecialchars($categoria->getId()) ?>" <?= ((int) $categoria->getId() === (int) $d->getIdCategoria()) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($categoria->getNomeCategoria()) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select" required>
                                            <?php $statusAtual = $d->getStatus(); ?>
                                            <option value="Aberto" <?= ($statusAtual === 'Aberto') ? 'selected' : '' ?>>Aberto</option>
                                            <option value="Em Andamento" <?= ($statusAtual === 'Em Andamento') ? 'selected' : '' ?>>Em
                                                Andamento</option>
                                            <option value="Resolvido" <?= ($statusAtual === 'Resolvido') ? 'selected' : '' ?>>Resolvido
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nova foto (opcional)</label>
                                        <input type="file" name="foto" class="form-control" accept="image/*">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Localização no Mapa (Clique para atualizar coordenadas):</label>
                                        <div id="mapa-edicao-<?= htmlspecialchars($d->getId()) ?>"
                                            style="height: 250px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px;">
                                        </div>
                                        <div class="alert alert-info small">
                                            <strong>Dica:</strong> Clique no mapa para atualizar a localização.
                                            <button type="button" class="btn btn-sm btn-secondary btn-geo"
                                                data-id="<?= htmlspecialchars($d->getId()) ?>">
                                                📍 Usar minha localização
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Latitude:</label>
                                                <input type="text" name="latitude" class="form-control latitude-input"
                                                    data-id="<?= htmlspecialchars($d->getId()) ?>" placeholder="Ex: -15.793889"
                                                    value="<?= htmlspecialchars($d->getLatitude() ?? '') ?>" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Longitude:</label>
                                                <input type="text" name="longitude" class="form-control longitude-input"
                                                    data-id="<?= htmlspecialchars($d->getId()) ?>" placeholder="Ex: -47.882778"
                                                    value="<?= htmlspecialchars($d->getLongitude() ?? '') ?>" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success btn-sm">Salvar edição</button>
                                </form>
                            </details>

                            <form
                                action="index.php?<?= htmlspecialchars($queryFiltrosPainel . ($queryFiltrosPainel !== '' ? '&' : '') . 'rota=processar_exclusao_denuncia') ?>"
                                method="POST" class="mt-3"
                                onsubmit="return confirm('Tem certeza que deseja excluir esta denúncia?');">
                                <input type="hidden" name="id_denuncia" value="<?= htmlspecialchars($d->getId()) ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Excluir denúncia</button>
                            </form>
                        <?php endif; ?>

                        <div class="mt-4 pt-3 border-top">
                            <h5 class="mb-3">Comentários</h5>

                            <form
                                action="index.php?<?= htmlspecialchars($queryFiltrosPainel . ($queryFiltrosPainel !== '' ? '&' : '') . 'rota=processar_comentario') ?>"
                                method="POST" class="js-comentar-denuncia mb-3"
                                data-lista-comentarios="comentarios-denuncia-<?= htmlspecialchars($d->getId()) ?>">
                                <input type="hidden" name="id_denuncia" value="<?= htmlspecialchars($d->getId()) ?>">
                                <textarea name="texto" rows="3" placeholder="Escreva um comentário..." required
                                    class="form-control"></textarea>
                                <button type="submit" class="btn btn-primary btn-sm mt-2">Comentar</button>
                            </form>

                            <div id="comentarios-denuncia-<?= htmlspecialchars($d->getId()) ?>">
                                <?php if (!empty($interacaoDenuncia['comentarios'])): ?>
                                    <?php foreach ($interacaoDenuncia['comentarios'] as $itemComentario): ?>
                                        <?php $comentario = $itemComentario['comentario']; ?>
                                        <div id="comentario-<?= htmlspecialchars($comentario->getId()) ?>"
                                            data-comentario-id="<?= htmlspecialchars($comentario->getId()) ?>" class="card mb-2">
                                            <div class="card-body py-2 px-3">
                                                <p class="mb-1">
                                                    <strong><?= htmlspecialchars($comentario->getNomeUsuario() ?? 'Usuário') ?></strong>
                                                </p>
                                                <p class="mb-1"><?= htmlspecialchars($comentario->getTexto()) ?></p>
                                                <small
                                                    class="text-muted"><?= htmlspecialchars($comentario->getDataComentario()) ?></small>

                                                <form
                                                    action="index.php?<?= htmlspecialchars($queryFiltrosPainel . ($queryFiltrosPainel !== '' ? '&' : '') . 'rota=processar_curtida_comentario') ?>"
                                                    method="POST" class="js-curtir-comentario mt-2 d-inline-block">
                                                    <input type="hidden" name="id_comentario"
                                                        value="<?= htmlspecialchars($comentario->getId()) ?>">
                                                    <button type="submit" class="btn btn-outline-secondary btn-sm"
                                                        data-botao-curtir-comentario>
                                                        <?= $itemComentario['usuarioCurtiu'] ? 'Descurtir comentário' : 'Curtir comentário' ?>
                                                    </button>
                                                </form>

                                                <span class="ms-2">Curtidas:
                                                    <strong
                                                        id="total-curtidas-comentario-<?= htmlspecialchars($comentario->getId()) ?>"><?= htmlspecialchars($itemComentario['totalCurtidas']) ?></strong></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted mb-0" data-sem-comentarios="<?= htmlspecialchars($d->getId()) ?>">Nenhum
                                        comentário ainda.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

            <?php if ($totalPaginas > 1): ?>
                <div class="d-flex flex-column gap-3 mt-4">
                    <?php if ($paginaAtual < $totalPaginas): ?>
                        <div>
                            <a class="btn btn-success"
                                href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=' . ($paginaAtual + 1)) ?>">Ver mais
                                denúncias</a>
                        </div>
                    <?php endif; ?>

                    <nav aria-label="Paginação das denúncias">
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
                                        href="index.php?<?= htmlspecialchars($baseQuery . '&pagina=' . ($paginaAtual + 1)) ?>">Próxima</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">Nenhuma denúncia registrada ainda. Seja o primeiro!</div>
        <?php endif; ?>
    </section>
</div>

<script>
    // Inicializa mapas Leaflet para edição de denúncias
    document.addEventListener('DOMContentLoaded', function () {
        const DEFAULT_LAT = -15.793889;
        const DEFAULT_LON = -47.882778;

        // Cria mapas para cada denúncia editável
        document.querySelectorAll('[id^="mapa-edicao-"]').forEach(function (mapElement) {
            const denunciaId = mapElement.id.replace('mapa-edicao-', '');
            const latInput = document.querySelector(`.latitude-input[data-id="${denunciaId}"]`);
            const lonInput = document.querySelector(`.longitude-input[data-id="${denunciaId}"]`);
            const btnGeo = document.querySelector(`.btn-geo[data-id="${denunciaId}"]`);

            if (typeof L === 'undefined' || !window.ProjetoETCLeaflet) {
                return;
            }

            // Coordenadas iniciais (da denúncia ou padrão)
            let initialLat = latInput.value ? parseFloat(latInput.value) : DEFAULT_LAT;
            let initialLon = lonInput.value ? parseFloat(lonInput.value) : DEFAULT_LON;

            const mapa = window.ProjetoETCLeaflet.criarMapa(mapElement, {
                center: [initialLat, initialLon],
                zoom: 13,
            }).map;

            let marcador = null;

            // Marca localização inicial se existir
            if (latInput.value && lonInput.value) {
                marcador = L.marker([initialLat, initialLon]).addTo(mapa);
            }

            // Clique no mapa para atualizar localização
            mapa.on('click', function (e) {
                const lat = e.latlng.lat.toFixed(8);
                const lon = e.latlng.lng.toFixed(8);

                latInput.value = lat;
                lonInput.value = lon;

                if (marcador) {
                    mapa.removeLayer(marcador);
                }

                marcador = L.marker([lat, lon]).addTo(mapa);
            });

            // Botão de geolocalização
            btnGeo.addEventListener('click', function (e) {
                e.preventDefault();
                if ('geolocation' in navigator) {
                    btnGeo.disabled = true;
                    const originalText = btnGeo.textContent;
                    btnGeo.textContent = '⏳ Localizando...';

                    navigator.geolocation.getCurrentPosition(
                        function (pos) {
                            const lat = pos.coords.latitude.toFixed(8);
                            const lon = pos.coords.longitude.toFixed(8);

                            latInput.value = lat;
                            lonInput.value = lon;

                            if (marcador) {
                                mapa.removeLayer(marcador);
                            }

                            marcador = L.marker([lat, lon]).addTo(mapa);
                            mapa.setView([lat, lon], 16);
                            btnGeo.disabled = false;
                            btnGeo.textContent = originalText;
                        },
                        function (err) {
                            alert('Erro ao obter localização: ' + err.message);
                            btnGeo.disabled = false;
                            btnGeo.textContent = originalText;
                        },
                        { timeout: 8000 }
                    );
                } else {
                    alert('Geolocalização não suportada no seu navegador.');
                }
            });
        });
    });
</script>

<?php include "view/templates/footer.php"; ?>