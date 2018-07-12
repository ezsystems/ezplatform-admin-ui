(function(global, doc) {
    [...doc.querySelectorAll('.ez-gmaplocation__map')].forEach((mapLocation) => {
        const latitude = parseFloat(mapLocation.dataset.latitude);
        const longitude = parseFloat(mapLocation.dataset.longitude);
        const map = global.L.map(mapLocation, {
            zoom: 15,
            zoomControl: false,
            scrollWheelZoom: false,
            dragging: false,
            tap: false,
            center: [latitude, longitude],
        });

        global.L.marker([latitude, longitude], {
            icon: new window.L.Icon.Default({
                imagePath: '/bundles/ezplatformadminuiassets/vendors/leaflet/dist/images/',
            }),
        }).addTo(map);

        global.L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);
    });
})(window, document);
