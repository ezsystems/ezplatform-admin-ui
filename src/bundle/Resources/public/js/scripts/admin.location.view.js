(function (global, doc) {
    const listContainers = [...doc.querySelectorAll('.ez-sil')];
    const mfuContainer = doc.querySelector('#ez-mfu');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const sortContainer = doc.querySelector('[data-sort-field][data-sort-order]');
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
        console.log(container.dataset);
        const items = JSON.parse(container.dataset.items).map(item => {
            item.content = item.content.Content;
            item.location = item.location.Location;

            return item;
        });
        const contentTypesMap = Object
            .values(JSON.parse(container.dataset.contentTypes))
            .reduce((total, {ContentTypeInfo}) => {
                total[ContentTypeInfo._href] = ContentTypeInfo;

                return total;
            }, {});

        global.ReactDOM.render(global.React.createElement(global.eZ.modules.SubItems, {
            parentLocationId: container.dataset.location,
            sortClauses: {[sortField]: sortOrder},
            restInfo: {token, siteaccess},
            // @TODO
            // discover content location view URL from backend routes
            locationViewLink: '/admin/content/location/{{locationId}}',
            extraActions: [{
                component: global.eZ.modules.MultiFileUpload,
                attrs: Object.assign({}, mfuAttrs, {
                    onPopupClose: (itemsUploaded) => {
                        if (itemsUploaded.length) {
                            window.location.reload(true);
                        }
                    },
                    popupOnly: false,
                    asButton: true
                })
            }],
            items,
            contentTypesMap,
            limit: parseInt(container.dataset.limit, 10)
        }), container);
    });
})(window, window.document);
