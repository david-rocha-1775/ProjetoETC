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
        <p>Visualizacao inicial do mapa. Integracao com dados reais sera adicionada em uma proxima etapa.</p>

        <div id="mapa-denuncias" style="height: 520px; border: 1px solid #ced4da; border-radius: 0.375rem;"></div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var mapContainer = document.getElementById('mapa-denuncias');

        if (!mapContainer || typeof L === 'undefined') {
            return;
        }

        var mapa = L.map('mapa-denuncias').setView([-23.55052, -46.633308], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(mapa);

        L.marker([-23.55052, -46.633308])
            .addTo(mapa)
            .bindPopup('Marcador de exemplo: integracao com denuncias em breve.');
    });
</script>

<?php include "view/templates/footer.php"; ?>