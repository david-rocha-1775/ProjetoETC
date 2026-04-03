<?php
$tituloPagina = 'Mapa de Denuncias';
$paginaCssExtra = [
    'assets/vendor/leaflet/leaflet.css',
];
$paginaJsHeadExtra = [
    'assets/vendor/leaflet/leaflet.js',
];
?>
<?php include "view/templates/header.php"; ?>

<div class="container mt-4 mb-4">
    <section aria-labelledby="titulo-mapa">
        <h2 id="titulo-mapa">Mapa de Denuncias</h2>
        <p>Visualizacao de denuncias por proximidade (raio de 7.5 km e limite de 50 pins).</p>

        <p id="mapa-status" aria-live="polite">Carregando mapa...</p>

        <div id="mapa-denuncias" style="height: 520px; border: 1px solid #ced4da; border-radius: 0.375rem;"></div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var mapContainer = document.getElementById('mapa-denuncias');
        var statusContainer = document.getElementById('mapa-status');

        if (!mapContainer || typeof L === 'undefined') {
            return;
        }

        var mapa = L.map('mapa-denuncias').setView([-15.793889, -47.882778], 12);
        var camadaMarcadores = L.layerGroup().addTo(mapa);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(mapa);

        function atualizarStatus(mensagem) {
            if (statusContainer) {
                statusContainer.textContent = mensagem;
            }
        }

        function limparMarcadores() {
            camadaMarcadores.clearLayers();
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
                throw new Error(dados.message || 'Nao foi possivel carregar as denuncias do mapa.');
            }

            var centro = dados.centro_usado;
            if (centro && typeof centro.latitude === 'number' && typeof centro.longitude === 'number') {
                mapa.setView([centro.latitude, centro.longitude], 13);
            }

            renderizarDenuncias(Array.isArray(dados.denuncias) ? dados.denuncias : []);

            atualizarStatus('Total de pins carregados: ' + (dados.total || 0) + ' (origem do centro: ' + (centro ? centro.origem : 'desconhecida') + ').');
        }

        function carregarComFallback() {
            if (!navigator.geolocation) {
                atualizarStatus('Geolocalizacao indisponivel. Carregando ponto padrao.');
                carregarDenuncias();
                return;
            }

            navigator.geolocation.getCurrentPosition(function (posicao) {
                carregarDenuncias(posicao.coords.latitude, posicao.coords.longitude)
                    .catch(function (erro) {
                        atualizarStatus(erro.message);
                    });
            }, function () {
                atualizarStatus('Nao foi possivel obter sua localizacao. Carregando ponto padrao.');
                carregarDenuncias().catch(function (erro) {
                    atualizarStatus(erro.message);
                });
            }, {
                enableHighAccuracy: true,
                timeout: 8000,
                maximumAge: 0
            });
        }

        carregarComFallback();
    });
</script>

<?php include "view/templates/footer.php"; ?>