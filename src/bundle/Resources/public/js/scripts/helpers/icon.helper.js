(function(global, doc, eZ) {
    const getIconPath = (path, iconSet = eZ.adminUiConfig.iconPaths.defaultIconSet) => {
        const iconSetPath = eZ.adminUiConfig.iconPaths.iconSets[iconSet];

        return `${iconSetPath}#${path}`;
    };

    eZ.addConfig('helpers.icon', {
        getIconPath,
    });
})(window, window.document, window.eZ);
