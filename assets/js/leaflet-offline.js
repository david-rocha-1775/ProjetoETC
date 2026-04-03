(function (window) {
    'use strict';

    if (!window.L) {
        return;
    }

    var DEFAULT_CENTER = [-15.793889, -47.882778];
    var DEFAULT_ZOOM = 13;
    var DEFAULT_TILE_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    var DEFAULT_OFFLINE_TILE_URL = 'assets/images/leaflet-offline-tile.svg';
    var DEFAULT_ATTRIBUTION = '&copy; OpenStreetMap contributors';
    var DEFAULT_OFFLINE_ATTRIBUTION = 'Mapa offline local';

    function criarMapa(target, options) {
        var config = options || {};
        var center = Array.isArray(config.center) ? config.center : DEFAULT_CENTER;
        var zoom = typeof config.zoom === 'number' ? config.zoom : DEFAULT_ZOOM;
        var tileUrl = config.tileUrl || DEFAULT_TILE_URL;
        var offlineTileUrl = config.offlineTileUrl || DEFAULT_OFFLINE_TILE_URL;
        var attribution = config.attribution || DEFAULT_ATTRIBUTION;
        var offlineAttribution = config.offlineAttribution || DEFAULT_OFFLINE_ATTRIBUTION;
        var maxZoom = typeof config.maxZoom === 'number' ? config.maxZoom : 19;
        var minZoom = typeof config.minZoom === 'number' ? config.minZoom : 1;
        var estaOffline = typeof navigator !== 'undefined' && navigator.onLine === false;
        var map = L.map(target).setView(center, zoom);
        var baseLayer = L.tileLayer(estaOffline ? offlineTileUrl : tileUrl, {
            attribution: estaOffline ? offlineAttribution : attribution,
            maxZoom: maxZoom,
            minZoom: minZoom,
            noWrap: true,
            errorTileUrl: offlineTileUrl,
        }).addTo(map);
        var avisadoOffline = false;

        if (!estaOffline && typeof config.onOfflineFallback === 'function') {
            baseLayer.on('tileerror', function () {
                if (avisadoOffline) {
                    return;
                }

                avisadoOffline = true;
                config.onOfflineFallback({
                    reason: 'tileerror',
                    tileUrl: tileUrl,
                    offlineTileUrl: offlineTileUrl,
                });
            });
        }

        if (estaOffline && typeof config.onOfflineFallback === 'function') {
            config.onOfflineFallback({
                reason: 'offline',
                tileUrl: tileUrl,
                offlineTileUrl: offlineTileUrl,
            });
        }

        return {
            map: map,
            baseLayer: baseLayer,
            isOffline: estaOffline,
            offlineTileUrl: offlineTileUrl,
        };
    }

    window.ProjetoETCLeaflet = {
        criarMapa: criarMapa,
        defaultCenter: DEFAULT_CENTER.slice(),
    };
})(window);