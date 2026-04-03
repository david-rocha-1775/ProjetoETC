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

<section class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-1"><?php echo isset($tituloPagina) ? htmlspecialchars($tituloPagina) : 'Nova Denúncia'; ?>
            </h2>
            <p class="text-muted mb-0">Bem-vindo,
                <?php echo isset($usuarioNome) ? htmlspecialchars($usuarioNome) : 'Usuário'; ?>
            </p>
        </div>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <?php $tipoMensagem = $_SESSION['tipo_mensagem'] ?? 'erro'; ?>
        <div class="alert <?php echo $tipoMensagem === 'sucesso' ? 'alert-success' : 'alert-danger'; ?> mb-4" role="alert">
            <p class="mb-0"><strong>Aviso:</strong>
                <?php echo htmlspecialchars($_SESSION['mensagem']); ?>
            </p>
        </div>
        <?php
        unset($_SESSION['mensagem']);
        unset($_SESSION['tipo_mensagem']);
        ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="index.php?rota=nova_denuncia" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título da Denúncia:</label>
                            <input type="text" id="titulo" name="titulo" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição (Detalhes):</label>
                            <textarea id="descricao" name="descricao" rows="5" class="form-control" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="localizacao" class="form-label">Localização (Bairro, Rua, Referência):</label>
                            <input type="text" id="localizacao" name="localizacao" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="id_categoria" class="form-label">Categoria da Denúncia:</label>
                            <select id="id_categoria" name="id_categoria" class="form-select" required>
                                <option value="">Selecione uma categoria...</option>
                                <?php if (isset($categorias) && is_array($categorias)): ?>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?php echo htmlspecialchars($categoria->getId()); ?>">
                                            <?php echo htmlspecialchars($categoria->getNomeCategoria()); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">Nenhuma categoria carregada</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="foto" class="form-label">Foto (Opcional):</label>
                            <input type="file" id="foto" name="foto" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Localização no Mapa (Clique para selecionar coordenadas):</label>
                            <div id="mapa-novo"
                                style="height: 300px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px;">
                            </div>
                            <div class="alert alert-info small">
                                <strong>Dica:</strong> Clique no mapa para marcar a localização exata da denúncia.
                                <button type="button" class="btn btn-sm btn-secondary" id="btn-geolocalizar">
                                    📍 Usar minha localização
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="latitude" class="form-label">Latitude:</label>
                                    <input type="text" id="latitude" name="latitude" class="form-control"
                                        placeholder="Ex: -15.793889" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="longitude" class="form-label">Longitude:</label>
                                    <input type="text" id="longitude" name="longitude" class="form-control"
                                        placeholder="Ex: -47.882778" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">Enviar Denúncia</button>
                            <a href="index.php?rota=painel" class="btn btn-outline-secondary">Cancelar e Voltar ao
                                Painel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Inicializa o mapa Leaflet para nova denúncia
    document.addEventListener('DOMContentLoaded', function () {
        const mapElement = document.getElementById('mapa-novo');
        const latInput = document.getElementById('latitude');
        const lonInput = document.getElementById('longitude');
        const btnGeo = document.getElementById('btn-geolocalizar');

        // Centro padrão (Brasília/DF)
        const DEFAULT_LAT = -15.793889;
        const DEFAULT_LON = -47.882778;

        if (typeof L === 'undefined' || !window.ProjetoETCLeaflet) {
            return;
        }

        const mapa = window.ProjetoETCLeaflet.criarMapa(mapElement, {
            center: [DEFAULT_LAT, DEFAULT_LON],
            zoom: 13,
        }).map;

        let marcador = null;

        // Clique no mapa para marcar localização
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
                        btnGeo.textContent = '📍 Usar minha localização';
                    },
                    function (err) {
                        alert('Erro ao obter localização: ' + err.message);
                        btnGeo.disabled = false;
                        btnGeo.textContent = '📍 Usar minha localização';
                    },
                    { timeout: 8000 }
                );
            } else {
                alert('Geolocalização não suportada no seu navegador.');
            }
        });
    });
</script>

<?php include "view/templates/footer.php"; ?>