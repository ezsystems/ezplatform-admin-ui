(function (global, doc, $) {
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
        const contentId = content._id;
        const checkVersionDraftLink = window.Routing.generate('ezplatform.version_draft.has_no_conflict', { contentId });
        const submitVersionEditForm = () => {
            doc.querySelector('#form_subitems_content_edit_content_info').value = contentId;
            doc.querySelector('#form_subitems_content_edit_version_info_content_info').value = contentId;
            doc.querySelector('#form_subitems_content_edit_version_info_version_no').value = content.CurrentVersion.Version.VersionInfo.versionNo;
            doc.querySelector(`#form_subitems_content_edit_language_${content.mainLanguageCode}`).checked = true;
            doc.querySelector('#form_subitems_content_edit_create').click();
        };
        const addDraft = () => {
            submitVersionEditForm();
            $('#version-draft-conflict-modal').modal('hide');
        };
        const showModal = (modalHtml) => {
            const wrapper = doc.querySelector('.ez-modal-wrapper');

            wrapper.innerHTML = modalHtml;
            wrapper.querySelector('.ez-btn--add-draft').addEventListener('click', addDraft, false);
            [...wrapper.querySelectorAll('.ez-btn--prevented')].forEach(btn => btn.addEventListener('click', event => event.preventDefault(), false));
            $('#version-draft-conflict-modal').modal('show');
        };
        fetch(checkVersionDraftLink, {
            credentials: 'same-origin'
        }).then(function (response) {
            // Status 409 means that a draft conflict has occurred and the modal must be displayed.
            // Otherwise we can go to Content Item edit page.
            if (response.status === 409) {
                response.text().then(showModal);
            } else if (response.status === 200) {
                submitVersionEditForm();
            }
        });
    };
    const generateLink = (locationId) => window.Routing.generate('_ezpublishLocation', { locationId });

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
            generateLink,
            parentLocationId: container.dataset.location,
            sortClauses: {[sortField]: sortOrder},
            restInfo: {token, siteaccess},
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
})(window, window.document, window.jQuery);
