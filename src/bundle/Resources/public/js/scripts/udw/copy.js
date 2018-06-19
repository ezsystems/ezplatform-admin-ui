(function(global, doc) {
    const btns = doc.querySelectorAll('.btn--udw-copy');
    const form = doc.querySelector('form[name="location_copy"]');
    const input = form.querySelector('#location_copy_new_parent_location');

    const onUdwConfirm = (items) => {
        input.value = items[0].id;
        form.submit();
    };
    const getUdwConfig = (event) => ({
        confirmLabel: 'Copy to location',
        title: 'Select location',
        multiple: false,
        startingLocationId: parseInt(event.currentTarget.dataset.rootLocation, 10),
        onConfirm: onUdwConfirm,
        allowContainersOnly: true,
    });
    const udwInitializer = global.eZ.UdwInitializer;

    udwInitializer.initialize(btns, getUdwConfig);
})(window, document);
