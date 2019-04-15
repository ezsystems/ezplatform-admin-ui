(function(global, doc, L) {
    doc.querySelectorAll('.ez-gmaplocation__map').forEach((mapLocation) => {
        const latitude = parseFloat(mapLocation.dataset.latitude);
        const longitude = parseFloat(mapLocation.dataset.longitude);
        const map = L.map(mapLocation, {
            zoom: 15,
            zoomControl: false,
            scrollWheelZoom: false,
            dragging: false,
            tap: false,
            center: [latitude, longitude],
        });

        L.marker([latitude, longitude], {
            icon: new L.Icon.Default({
                imagePath: '/bundles/ezplatformadminuiassets/vendors/leaflet/dist/images/',
            }),
        }).addTo(map);

        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);
    });
})(window, document, window.L);
