(function(global, doc, Translator) {
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
        const attribution = Translator.trans(
            /*@Desc("&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors")*/ 'attribution',
            {},
            'admin_ui_frontend_admin_location_load_map'
        );

        global.L.marker([latitude, longitude], {
            icon: new window.L.Icon.Default({
                imagePath: '/bundles/ezplatformadminuiassets/vendors/leaflet/dist/images/',
            }),
        }).addTo(map);

        global.L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution,
        }).addTo(map);
    });
})(window, document, window.Translator);
