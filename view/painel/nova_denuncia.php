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
    <div class="row mb-4 nova-denuncia-header animate-in">
        <div class="col-12">
            <h2 class="mb-2"><?php echo isset($tituloPagina) ? htmlspecialchars($tituloPagina) : 'Nova Denúncia'; ?>
            </h2>
            <p class="text-muted mb-0">Contribua para uma cidade melhor. Registre ocorrências, problemas de
                infraestrutura ou solicitações de serviços públicos.</p>
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

    <div class="row g-4 nova-denuncia-page">
        <div class="col-12 col-xl-8">
            <form id="form-nova-denuncia" action="index.php?rota=nova_denuncia" method="POST"
                enctype="multipart/form-data" class="d-grid gap-4">
                <?= csrfField() ?>

                <div class="card shadow-sm nova-denuncia-card animate-in">
                    <div class="card-body">
                        <h3 class="nova-denuncia-card-title">Identificação</h3>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="titulo" class="form-label">Título da ocorrência *</label>
                                <input type="text" id="titulo" name="titulo" class="form-control"
                                    placeholder="Ex: Buraco na via principal" required>
                            </div>
                            <div class="col-12 col-md-7">
                                <label for="id_categoria" class="form-label">Categoria *</label>
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
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm nova-denuncia-card animate-in">
                    <div class="card-body">
                        <h3 class="nova-denuncia-card-title">Detalhes e mídia</h3>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição detalhada *</label>
                            <textarea id="descricao" name="descricao" rows="5" class="form-control"
                                placeholder="Descreva o problema com o máximo de detalhes possível para facilitar o atendimento."
                                required></textarea>
                        </div>

                        <div class="mb-0">
                            <label for="foto" class="form-label">Anexo *</label>
                            <input type="file" id="foto" name="foto" class="form-control" accept="image/*"
                                required>
                            <p class="small text-muted mt-2 mb-0">Obrigatório: envie uma imagem de até 5MB.
                            </p>

                            <div id="preview-anexo" class="nova-denuncia-preview d-none mt-3" aria-live="polite">
                                <img id="preview-anexo-img" class="nova-denuncia-preview-img d-none"
                                    alt="Pré-visualização do anexo selecionado">
                                <div id="preview-anexo-arquivo" class="nova-denuncia-preview-file d-none"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <aside class="col-12 col-xl-4">
            <div class="d-grid gap-4">
                <div class="card shadow-sm nova-denuncia-card nova-denuncia-sidecard animate-in">
                    <div class="card-body">
                        <h3 class="nova-denuncia-card-title">Localização</h3>
                        <div class="mb-3">
                            <label for="localizacao" class="form-label">Localização (bairro, rua e referência) *</label>
                            <input type="text" id="localizacao" name="localizacao" form="form-nova-denuncia"
                                class="form-control" required>
                        </div>

                        <label class="form-label">Localização no mapa</label>
                        <div id="mapa-novo" class="nova-denuncia-mapa mb-3"></div>

                        <div class="nova-denuncia-info mb-3">
                            <div>
                                <strong>Dica:</strong> Clique no mapa para marcar a localização exata da denúncia.
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-geolocalizar">
                                Usar minha localização
                            </button>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" id="latitude" name="latitude" form="form-nova-denuncia"
                                    class="form-control" placeholder="Ex: -15.793889" readonly>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" id="longitude" name="longitude" form="form-nova-denuncia"
                                    class="form-control" placeholder="Ex: -47.882778" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm nova-denuncia-card nova-denuncia-sidecard animate-in">
                    <div class="card-body">
                        <button type="submit" form="form-nova-denuncia"
                            class="btn btn-primary nova-denuncia-btn-principal w-100">Enviar denúncia</button>
                        <a href="index.php?rota=painel" class="btn btn-link text-decoration-none mt-2 px-0">Cancelar e
                            voltar ao painel</a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</section>

<script>
    // Inicializa o mapa Leaflet para nova denúncia
    document.addEventListener('DOMContentLoaded', function () {
        const mapElement = document.getElementById('mapa-novo');
        const latInput = document.getElementById('latitude');
        const lonInput = document.getElementById('longitude');
        const btnGeo = document.getElementById('btn-geolocalizar');
        const inputFoto = document.getElementById('foto');
        const previewWrapper = document.getElementById('preview-anexo');
        const previewImagem = document.getElementById('preview-anexo-img');
        const previewArquivo = document.getElementById('preview-anexo-arquivo');

        // Centro padrão (Brasília/DF)
        const DEFAULT_LAT = -15.793889;
        const DEFAULT_LON = -47.882778;

        if (typeof L === 'undefined' || !window.ProjetoETCLeaflet) {
            return;
        }

        function limparPreviewAnexo() {
            if (!previewWrapper || !previewImagem || !previewArquivo) {
                return;
            }
            previewWrapper.classList.add('d-none');
            previewImagem.classList.add('d-none');
            previewArquivo.classList.add('d-none');
            previewImagem.removeAttribute('src');
            previewArquivo.textContent = '';
        }

        function renderizarPreviewAnexo(file) {
            if (!previewWrapper || !previewImagem || !previewArquivo) {
                return;
            }

            if (!file) {
                limparPreviewAnexo();
                return;
            }

            previewWrapper.classList.remove('d-none');

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    previewImagem.src = event.target.result;
                    previewImagem.classList.remove('d-none');
                    previewArquivo.classList.add('d-none');
                };
                reader.readAsDataURL(file);
                return;
            }

            previewImagem.classList.add('d-none');
            previewArquivo.classList.remove('d-none');
            previewArquivo.textContent = 'Arquivo selecionado: ' + file.name;
        }

        if (inputFoto) {
            inputFoto.addEventListener('change', function () {
                const arquivo = inputFoto.files && inputFoto.files[0] ? inputFoto.files[0] : null;
                renderizarPreviewAnexo(arquivo);
            });
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
                        btnGeo.textContent = 'Usar minha localização';
                    },
                    function (err) {
                        alert('Erro ao obter localização: ' + err.message);
                        btnGeo.disabled = false;
                        btnGeo.textContent = 'Usar minha localização';
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