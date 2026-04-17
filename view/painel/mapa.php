<?php
$tituloPagina = 'Mapa de Denúncias';
$paginaCssExtra = [
    'assets/vendor/leaflet/leaflet.css',
];
$paginaJsHeadExtra = [
    'assets/vendor/leaflet/leaflet.js',
    'assets/js/leaflet-offline.js',
];
?>
<?php include "view/templates/header.php"; ?>

<div class="container mt-4 mb-4">
    <section aria-labelledby="titulo-mapa">
        <h2 id="titulo-mapa">Mapa de Denúncias</h2>
        <p>Visualização de denúncias por proximidade (raio de 10 km e limite de 50 pins).</p>

        <p id="mapa-status" aria-live="polite">Carregando mapa...</p>

        <div class="row g-2 mb-3" id="painel-centro-mapa">
            <div class="col-12 col-md-3">
                <label for="mapa-centro-lat" class="form-label">Latitude</label>
                <input type="number" step="0.00000001" class="form-control" id="mapa-centro-lat"
                    placeholder="Ex: -15.793889">
            </div>
            <div class="col-12 col-md-3">
                <label for="mapa-centro-lon" class="form-label">Longitude</label>
                <input type="number" step="0.00000001" class="form-control" id="mapa-centro-lon"
                    placeholder="Ex: -47.882778">
            </div>
            <div class="col-12 col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" id="btn-aplicar-centro">Aplicar centro</button>
            </div>
            <div class="col-12 col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-outline-secondary w-100" id="btn-usar-localizacao">Usar minha
                    localização</button>
            </div>
        </div>

        <div id="mapa-denuncias" class="mapa-denuncias-canvas"></div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var mapContainer = document.getElementById('mapa-denuncias');
        var statusContainer = document.getElementById('mapa-status');
        var campoCentroLat = document.getElementById('mapa-centro-lat');
        var campoCentroLon = document.getElementById('mapa-centro-lon');
        var botaoAplicarCentro = document.getElementById('btn-aplicar-centro');
        var botaoUsarLocalizacao = document.getElementById('btn-usar-localizacao');

        if (!mapContainer || typeof L === 'undefined' || !window.ProjetoETCLeaflet) {
            return;
        }

        var centroPadrao = [-15.793889, -47.882778];
        var mapa = window.ProjetoETCLeaflet.criarMapa('mapa-denuncias', {
            center: centroPadrao,
            zoom: 12,
            onOfflineFallback: function () {
                atualizarStatus('Sem acesso aos tiles externos. Usando fundo offline local.');
            }
        }).map;
        var camadaMarcadores = L.layerGroup().addTo(mapa);
        var marcadorCentro = null;
        var circuloRaio = null;
        var raioKm = 10;

        function atualizarStatus(mensagem) {
            if (statusContainer) {
                statusContainer.textContent = mensagem;
            }
        }

        function limparMarcadores() {
            camadaMarcadores.clearLayers();
        }

        function atualizarPainelCentro(latitude, longitude) {
            if (campoCentroLat) {
                campoCentroLat.value = latitude.toFixed(8);
            }

            if (campoCentroLon) {
                campoCentroLon.value = longitude.toFixed(8);
            }
        }

        function atualizarCentroMapa(latitude, longitude, recarregar) {
            mapa.setView([latitude, longitude], 13);
            atualizarPainelCentro(latitude, longitude);

            if (marcadorCentro) {
                mapa.removeLayer(marcadorCentro);
            }

            if (circuloRaio) {
                mapa.removeLayer(circuloRaio);
            }

            marcadorCentro = L.marker([latitude, longitude], { draggable: true }).addTo(mapa);
            circuloRaio = L.circle([latitude, longitude], {
                radius: raioKm * 1000,
                color: '#0d6efd',
                fillColor: '#0d6efd',
                fillOpacity: 0.08
            }).addTo(mapa);

            marcadorCentro.on('dragend', function (evento) {
                var posicao = evento.target.getLatLng();
                atualizarCentroMapa(posicao.lat, posicao.lng, true);
            });

            if (recarregar !== false) {
                carregarDenuncias(latitude, longitude).catch(function (erro) {
                    atualizarStatus(erro.message);
                });
            }
        }

        function escaparHtml(valor) {
            return String(valor)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderizarDenuncias(denuncias) {
            limparMarcadores();

            denuncias.forEach(function (denuncia) {
                if (typeof denuncia.latitude !== 'number' || typeof denuncia.longitude !== 'number') {
                    return;
                }

                var popup = '<strong>' + escaparHtml(denuncia.titulo) + '</strong><br>' +
                    'Status: ' + escaparHtml(denuncia.status) + '<br>' +
                    'Local: ' + escaparHtml(denuncia.localizacao);

                L.marker([denuncia.latitude, denuncia.longitude])
                    .addTo(camadaMarcadores)
                    .bindPopup(popup);
            });
        }

        async function carregarDenuncias(latitude, longitude) {
            var url = 'index.php?rota=listar_denuncias_mapa';

            if (typeof latitude === 'number' && typeof longitude === 'number') {
                url += '&lat=' + encodeURIComponent(latitude) + '&lon=' + encodeURIComponent(longitude);
            }

            var resposta = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            var dados = await resposta.json();
            if (!resposta.ok || !dados.success) {
                throw new Error(dados.message || 'Não foi possível carregar as denúncias do mapa.');
            }

            var centro = dados.centro_usado;
            if (centro && typeof centro.latitude === 'number' && typeof centro.longitude === 'number') {
                atualizarCentroMapa(centro.latitude, centro.longitude, false);
            }

            renderizarDenuncias(Array.isArray(dados.denuncias) ? dados.denuncias : []);

            atualizarStatus('Total de pins carregados: ' + (dados.total || 0) + ' (origem do centro: ' + (centro ? centro.origem : 'desconhecida') + ').');
        }

        function carregarComFallback() {
            if (!navigator.geolocation) {
                atualizarStatus('Geolocalização indisponível. Carregando ponto padrão.');
                carregarDenuncias();
                return;
            }

            navigator.geolocation.getCurrentPosition(function (posicao) {
                carregarDenuncias(posicao.coords.latitude, posicao.coords.longitude)
                    .catch(function (erro) {
                        atualizarStatus(erro.message);
                    });
            }, function () {
                atualizarStatus('Não foi possível obter sua localização. Carregando ponto padrão.');
                carregarDenuncias().catch(function (erro) {
                    atualizarStatus(erro.message);
                });
            }, {
                enableHighAccuracy: true,
                timeout: 8000,
                maximumAge: 0
            });
        }

        if (botaoAplicarCentro) {
            botaoAplicarCentro.addEventListener('click', function () {
                var latitude = parseFloat(campoCentroLat ? campoCentroLat.value : '');
                var longitude = parseFloat(campoCentroLon ? campoCentroLon.value : '');

                if (Number.isNaN(latitude) || Number.isNaN(longitude)) {
                    atualizarStatus('Informe latitude e longitude válidas para aplicar o centro.');
                    return;
                }

                atualizarCentroMapa(latitude, longitude, true);
            });
        }

        if (botaoUsarLocalizacao && navigator.geolocation) {
            botaoUsarLocalizacao.addEventListener('click', function () {
                navigator.geolocation.getCurrentPosition(function (posicao) {
                    atualizarCentroMapa(posicao.coords.latitude, posicao.coords.longitude, true);
                }, function () {
                    atualizarStatus('Não foi possível obter sua localização.');
                }, {
                    enableHighAccuracy: true,
                    timeout: 8000,
                    maximumAge: 0
                });
            });
        }

        mapa.on('click', function (evento) {
            atualizarCentroMapa(evento.latlng.lat, evento.latlng.lng, true);
        });

        carregarComFallback();
    });
</script>

<?php include "view/templates/footer.php"; ?>