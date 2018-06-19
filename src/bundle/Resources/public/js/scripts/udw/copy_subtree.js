(function(global, doc) {
    const btns = [...doc.querySelectorAll('.ez-btn--udw-copy-subtree')];
    const form = doc.querySelector('form[name="location_copy_subtree"]');
    const input = form.querySelector('#location_copy_subtree_new_parent_location');

    const onUdwConfirm = (items) => {
        input.value = items[0].id;
        form.submit();
    };
    const getUdwConfig = (event) => ({
        allowContainersOnly: true,
        confirmLabel: 'View content',
        title: 'Select location',
        multiple: false,
        startingLocationId: parseInt(event.currentTarget.dataset.rootLocation, 10),
        onConfirm: onUdwConfirm,
    });
    const udwInitializer = global.eZ.UdwInitializer;

    udwInitializer.initialize(btns, getUdwConfig);
})(window, document);
