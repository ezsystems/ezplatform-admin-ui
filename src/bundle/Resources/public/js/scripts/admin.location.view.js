(function () {
    const listContainers = [...document.querySelectorAll('.ez-sil')];
    const mfuContainer = document.querySelector('#ez-mfu');
    const token = document.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
    const sortContainer = document.querySelector('[data-sort-field][data-sort-order]');
    const sortField = sortContainer.getAttribute('data-sort-field');
    const sortOrder = sortContainer.getAttribute('data-sort-order');
    const mfuAttrs = {
        adminUiConfig: Object.assign({}, window.eZ.adminUiConfig, {
            token,
            siteaccess
        }),
        parentInfo: {
            contentTypeIdentifier: mfuContainer.dataset.parentContentTypeIdentifier,
            contentTypeId: mfuContainer.dataset.parentContentTypeId,
            locationPath: mfuContainer.dataset.parentLocationPath,
            language: mfuContainer.dataset.parentContentLanguage
        },
    };

    listContainers.forEach(container => {
        ReactDOM.render(React.createElement(eZ.modules.SubItems, {
            parentLocationId: container.dataset.location,
            sortClauses: {[sortField]: sortOrder},
            restInfo: {token, siteaccess},
            // @TODO
            // discover content location view URL from backend routes
            locationViewLink: '/admin/content/location/{{locationId}}',
            extraActions: [{
                component: eZ.modules.MultiFileUpload,
                attrs: Object.assign({}, mfuAttrs, {
                    onPopupClose: (itemsUploaded) => {
                        if (itemsUploaded.length) {
                            window.location.reload(true);
                        }
                    },
                    popupOnly: false,
                    asButton: true
                })
            }]
        }), container);
    });
})();
