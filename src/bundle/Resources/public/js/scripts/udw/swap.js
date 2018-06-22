(function(global, doc) {
    const btns = doc.querySelectorAll('.btn--udw-swap');
    const form = doc.querySelector('form[name="location_swap"]');
    const input = form.querySelector('#location_swap_new_location');

    const onUdwConfirm = (items) => {
        input.value = items[0].id;
        form.submit();
    };
    const getUdwConfig = () => ({
        confirmLabel: 'Swap location',
        title: 'Select location to be swapped with',
        multiple: false,
        startingLocationId: global.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
        onConfirm: onUdwConfirm,
    });
    const udwInitializer = global.eZ.UdwInitializer;

    udwInitializer.initialize(btns, getUdwConfig);
})(window, document);
