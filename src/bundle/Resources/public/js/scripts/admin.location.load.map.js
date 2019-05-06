(function(global, doc, Leaflet) {
    doc.querySelectorAll('.ez-gmaplocation__map').forEach((mapLocation) => {
        const latitude = parseFloat(mapLocation.dataset.latitude);
        const longitude = parseFloat(mapLocation.dataset.longitude);
        const map = Leaflet.map(mapLocation, {
            zoom: 15,
            zoomControl: false,
            scrollWheelZoom: false,
            dragging: false,
            tap: false,
            center: [latitude, longitude],
        });

        Leaflet.marker([latitude, longitude], {
            icon: new Leaflet.Icon.Default({
                imagePath: '/bundles/ezplatformadminuiassets/vendors/leaflet/dist/images/',
            }),
        }).addTo(map);

        Leaflet.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);
    });
})(window, window.document, window.L);
