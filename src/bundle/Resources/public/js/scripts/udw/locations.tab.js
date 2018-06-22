(function(global, doc) {
    const btns = doc.querySelectorAll('.btn--udw-add');
    const submitButton = doc.querySelector('#content_location_add_add');
    const form = doc.querySelector('form[name="content_location_add"]');
    const input = form.querySelector('#content_location_add_new_locations');

    const canSelectContent = ({ item }, callback) => callback(item.ContentInfo.Content.ContentTypeInfo.isContainer);
    const beforeUdwOpen = (event) => {
        event.stopPropagation();
    };
    const onUdwConfirm = (items) => {
        input.value = items[0].id;
        submitButton.click();
    };
    const getUdwConfig = () => ({
        canSelectContent,
        confirmLabel: 'Add location',
        title: 'Select location',
        multiple: false,
        startingLocationId: global.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
        onConfirm: onUdwConfirm,
    });
    const udwInitializer = global.eZ.UdwInitializer;

    udwInitializer.initialize(btns, getUdwConfig, beforeUdwOpen);
})(window, document);
