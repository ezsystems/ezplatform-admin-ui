(function (global, doc) {
    const listContainers = [...doc.querySelectorAll('.ez-sil')];
    const mfuContainer = doc.querySelector('#ez-mfu');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const sortContainer = doc.querySelector('[data-sort-field][data-sort-order]');
    const sortField = sortContainer.getAttribute('data-sort-field');
    const sortOrder = sortContainer.getAttribute('data-sort-order');
    const mfuAttrs = {
        adminUiConfig: Object.assign({}, global.eZ.adminUiConfig, {
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
    const handleEditItem = (content) => {
        doc.querySelector('#form_subitems_content_edit_content_info').value = content._id;
        doc.querySelector('#form_subitems_content_edit_version_info_content_info').value = content._id;
        doc.querySelector('#form_subitems_content_edit_version_info_version_no').value = content.CurrentVersion.Version.VersionInfo.versionNo;
        doc.querySelector(`#form_subitems_content_edit_language_${content.mainLanguageCode}`).checked = true;

        doc.querySelector('#form_subitems_content_edit_create').click();
    };

    listContainers.forEach(container => {
        const subItemsList = JSON.parse(container.dataset.items).SubitemsList;
        const items = subItemsList.SubitemsRow.map(item => ({
            content: item.Content,
            location: item.Location
        }));
        const contentTypes = JSON.parse(container.dataset.contentTypes).ContentTypeInfoList.ContentType;
        const contentTypesMap = contentTypes.reduce((total, item) => {
            total[item._href] = item;

            return total;
        }, {});

        global.ReactDOM.render(global.React.createElement(global.eZ.modules.SubItems, {
            handleEditItem,
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
            limit: parseInt(container.dataset.limit, 10),
            totalCount: subItemsList.ChildrenCount
        }), container);
    });
})(window, window.document);
