(function(global, doc, eZ) {
    const getIconPath = (path) => {
        return `/bundles/ezplatformadminuiassets/vendors/webalys/streamlineicons/all-icons.svg#${path}`;
    };

    eZ.addConfig('helpers.icon', {
        getIconPath,
    });
})(window, window.document, window.eZ);
