(function(global, doc) {
    const btns = doc.querySelectorAll('.btn--udw-move');
    const form = doc.querySelector('form[name="location_move"]');
    const input = form.querySelector('#location_move_new_parent_location');

    const canSelectContent = ({ item }, callback) => callback(item.ContentInfo.Content.ContentTypeInfo.isContainer);
    const onUdwConfirm = (items) => {
        input.value = items[0].id;
        form.submit();
    };
    const getUdwConfig = (event) => ({
        confirmLabel: 'Move to location',
        title: 'Select destination',
        canSelectContent,
        multiple: false,
        startingLocationId: parseInt(event.currentTarget.dataset.rootLocation, 10),
        onConfirm: onUdwConfirm,
        allowContainersOnly: true,
    });
    const udwInitializer = global.eZ.UdwInitializer;

    udwInitializer.initialize(btns, getUdwConfig);
})(window, document);
