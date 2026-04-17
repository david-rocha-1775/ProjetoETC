<?php
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

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <h2 class="mb-0">Detalhes da denúncia</h2>
        <a href="index.php?<?= htmlspecialchars($painelQueryRetorno) ?>" class="btn btn-outline-secondary">Voltar ao
            painel</a>
    </div>

    <article id="denuncia-<?= htmlspecialchars((string) $denuncia->getId()) ?>"
        data-denuncia-id="<?= htmlspecialchars((string) $denuncia->getId()) ?>" class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                <h3 class="card-title mb-0"><?= htmlspecialchars($denuncia->getTitulo()) ?></h3>
                <span class="badge text-bg-secondary"><?= htmlspecialchars($nomeCategoriaDenuncia) ?></span>
            </div>

            <p class="mb-1"><strong>Status:</strong> <?= htmlspecialchars($denuncia->getStatus()) ?></p>
            <p class="mb-1"><strong>Localização:</strong> <?= htmlspecialchars($denuncia->getLocalizacao()) ?></p>
            <p class="mb-1"><strong>Autor:</strong> <?= htmlspecialchars($nomeAutorDenuncia) ?></p>
            <p class="mb-3"><strong>Criada em:</strong> <?= htmlspecialchars((string) $denuncia->getDataCriacao()) ?>
            </p>

            <p class="mb-3"><?= nl2br(htmlspecialchars($denuncia->getDescricao())) ?></p>

            <div class="d-inline-flex align-items-center gap-2 mb-3 mt-1">
                <form action="index.php?rota=processar_curtida_denuncia" method="POST" class="js-curtir-denuncia">
                    <?= csrfField() ?>
                    <input type="hidden" name="id_denuncia"
                        value="<?= htmlspecialchars((string) $denuncia->getId()) ?>">
                    <input type="hidden" name="retorno_rota" value="detalhe_denuncia">
                    <input type="hidden" name="retorno_id" value="<?= htmlspecialchars((string) $denuncia->getId()) ?>">
                    <button type="submit" class="btn btn-outline-primary btn-sm" data-botao-curtir-denuncia>
                        <?= $interacaoDenuncia['usuarioCurtiu'] ? 'Descurtir' : 'Curtir' ?>
                    </button>
                </form>
                <span>Curtidas:
                    <strong
                        id="total-curtidas-denuncia-<?= htmlspecialchars((string) $denuncia->getId()) ?>"><?= htmlspecialchars((string) $interacaoDenuncia['totalCurtidas']) ?></strong></span>
            </div>

            <?php if ($denuncia->getFotoPath()): ?>
                <img src="<?= htmlspecialchars($denuncia->getFotoPath()) ?>" alt="Foto da denúncia"
                    class="img-fluid rounded border mb-3 detalhe-denuncia-foto">
            <?php endif; ?>

            <?php if ($podeGerenciar): ?>
                <details class="mt-3">
                    <summary><strong>Editar denúncia</strong></summary>
                    <form action="index.php?rota=processar_edicao_denuncia" method="POST" enctype="multipart/form-data"
                        class="mt-3">
                        <?= csrfField() ?>
                        <input type="hidden" name="id_denuncia"
                            value="<?= htmlspecialchars((string) $denuncia->getId()) ?>">
                        <input type="hidden" name="retorno_rota" value="detalhe_denuncia">
                        <input type="hidden" name="retorno_id" value="<?= htmlspecialchars((string) $denuncia->getId()) ?>">

                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control"
                                value="<?= htmlspecialchars($denuncia->getTitulo()) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" rows="3" class="form-control"
                                required><?= htmlspecialchars($denuncia->getDescricao()) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Localização</label>
                            <input type="text" name="localizacao" class="form-control"
                                value="<?= htmlspecialchars($denuncia->getLocalizacao()) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Categoria</label>
                            <select name="id_categoria" class="form-select" required>
                                <?php if (isset($categorias) && is_array($categorias)): ?>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= htmlspecialchars($categoria->getId()) ?>" <?= ((int) $categoria->getId() === (int) $denuncia->getIdCategoria()) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($categoria->getNomeCategoria()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <?php $statusAtual = $denuncia->getStatus(); ?>
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
                            <label class="form-label">Localização no mapa (clique para atualizar coordenadas):</label>
                            <div id="mapa-edicao-<?= htmlspecialchars((string) $denuncia->getId()) ?>"
                                class="detalhe-mapa-edicao">
                            </div>
                            <div class="alert alert-info small">
                                <strong>Dica:</strong> Clique no mapa para atualizar a localização.
                                <button type="button" class="btn btn-sm btn-secondary btn-geo"
                                    data-id="<?= htmlspecialchars((string) $denuncia->getId()) ?>">
                                    Usar minha localização
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Latitude:</label>
                                    <input type="text" name="latitude" class="form-control latitude-input"
                                        data-id="<?= htmlspecialchars((string) $denuncia->getId()) ?>"
                                        value="<?= htmlspecialchars((string) ($denuncia->getLatitude() ?? '')) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Longitude:</label>
                                    <input type="text" name="longitude" class="form-control longitude-input"
                                        data-id="<?= htmlspecialchars((string) $denuncia->getId()) ?>"
                                        value="<?= htmlspecialchars((string) ($denuncia->getLongitude() ?? '')) ?>"
                                        readonly>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-sm">Salvar edição</button>
                    </form>
                </details>

                <form action="index.php?rota=processar_exclusao_denuncia" method="POST" class="mt-3"
                    onsubmit="return confirm('Tem certeza que deseja excluir esta denúncia?');">
                    <?= csrfField() ?>
                    <input type="hidden" name="id_denuncia" value="<?= htmlspecialchars((string) $denuncia->getId()) ?>">
                    <input type="hidden" name="retorno_rota" value="detalhe_denuncia">
                    <input type="hidden" name="retorno_id" value="<?= htmlspecialchars((string) $denuncia->getId()) ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Excluir denúncia</button>
                </form>
            <?php endif; ?>
        </div>
    </article>

    <section class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title mb-3">Comentários</h4>

            <form action="index.php?rota=processar_comentario" method="POST" class="js-comentar-denuncia mb-3"
                data-lista-comentarios="comentarios-denuncia-<?= htmlspecialchars((string) $denuncia->getId()) ?>">
                <?= csrfField() ?>
                <input type="hidden" name="id_denuncia" value="<?= htmlspecialchars((string) $denuncia->getId()) ?>">
                <input type="hidden" name="retorno_rota" value="detalhe_denuncia">
                <input type="hidden" name="retorno_id" value="<?= htmlspecialchars((string) $denuncia->getId()) ?>">
                <textarea name="texto" rows="3" placeholder="Escreva um comentário..." required
                    class="form-control"></textarea>
                <button type="submit" class="btn btn-primary btn-sm mt-2">Comentar</button>
            </form>

            <div id="comentarios-denuncia-<?= htmlspecialchars((string) $denuncia->getId()) ?>">
                <?php if (!empty($interacaoDenuncia['comentarios'])): ?>
                    <?php foreach ($interacaoDenuncia['comentarios'] as $itemComentario): ?>
                        <?php $comentario = $itemComentario['comentario']; ?>
                        <div id="comentario-<?= htmlspecialchars((string) $comentario->getId()) ?>"
                            data-comentario-id="<?= htmlspecialchars((string) $comentario->getId()) ?>" class="card mb-2">
                            <div class="card-body py-2 px-3">
                                <p class="mb-1">
                                    <strong><?= htmlspecialchars($comentario->getNomeUsuario() ?? 'Usuário') ?></strong>
                                </p>
                                <p class="mb-1"><?= htmlspecialchars($comentario->getTexto()) ?></p>
                                <small
                                    class="text-muted"><?= htmlspecialchars((string) $comentario->getDataComentario()) ?></small>

                                <form action="index.php?rota=processar_curtida_comentario" method="POST"
                                    class="js-curtir-comentario mt-2 d-inline-block">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id_comentario"
                                        value="<?= htmlspecialchars((string) $comentario->getId()) ?>">
                                    <input type="hidden" name="retorno_rota" value="detalhe_denuncia">
                                    <input type="hidden" name="retorno_id"
                                        value="<?= htmlspecialchars((string) $denuncia->getId()) ?>">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm" data-botao-curtir-comentario>
                                        <?= $itemComentario['usuarioCurtiu'] ? 'Descurtir comentário' : 'Curtir comentário' ?>
                                    </button>
                                </form>

                                <span class="ms-2">Curtidas:
                                    <strong
                                        id="total-curtidas-comentario-<?= htmlspecialchars((string) $comentario->getId()) ?>"><?= htmlspecialchars((string) $itemComentario['totalCurtidas']) ?></strong></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted mb-0" data-sem-comentarios="<?= htmlspecialchars((string) $denuncia->getId()) ?>">
                        Nenhum comentário ainda.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const DEFAULT_LAT = -15.793889;
        const DEFAULT_LON = -47.882778;
        const mapElement = document.getElementById('mapa-edicao-<?= htmlspecialchars((string) $denuncia->getId()) ?>');
        const denunciaId = '<?= htmlspecialchars((string) $denuncia->getId()) ?>';

        if (!mapElement) {
            return;
        }

        const latInput = document.querySelector('.latitude-input[data-id="' + denunciaId + '"]');
        const lonInput = document.querySelector('.longitude-input[data-id="' + denunciaId + '"]');
        const btnGeo = document.querySelector('.btn-geo[data-id="' + denunciaId + '"]');

        if (typeof L === 'undefined' || !window.ProjetoETCLeaflet || !latInput || !lonInput || !btnGeo) {
            return;
        }

        let initialLat = latInput.value ? parseFloat(latInput.value) : DEFAULT_LAT;
        let initialLon = lonInput.value ? parseFloat(lonInput.value) : DEFAULT_LON;

        const mapa = window.ProjetoETCLeaflet.criarMapa(mapElement, {
            center: [initialLat, initialLon],
            zoom: 13,
        }).map;

        let marcador = null;
        if (latInput.value && lonInput.value) {
            marcador = L.marker([initialLat, initialLon]).addTo(mapa);
        }

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

        btnGeo.addEventListener('click', function (e) {
            e.preventDefault();

            if (!('geolocation' in navigator)) {
                alert('Geolocalização não suportada no seu navegador.');
                return;
            }

            btnGeo.disabled = true;
            const originalText = btnGeo.textContent;
            btnGeo.textContent = 'Localizando...';

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
        });
    });
</script>

<?php include "view/templates/footer.php"; ?>