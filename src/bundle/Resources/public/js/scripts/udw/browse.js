(function(global, doc) {
    const btns = doc.querySelectorAll('.btn--udw-browse');
    const onUdwConfirm = (items) => {
        window.location.href = window.Routing.generate('_ezpublishLocation', { locationId: items[0].id });
    };
    const getUdwConfig = (event) => ({
        confirmLabel: 'View content',
        title: 'Browse content',
        multiple: false,
        startingLocationId: parseInt(event.currentTarget.dataset.startingLocationId, 10),
        onConfirm: onUdwConfirm,
    });
    const udwInitializer = global.eZ.UdwInitializer;

    udwInitializer.initialize(btns, getUdwConfig);
})(window, document);
