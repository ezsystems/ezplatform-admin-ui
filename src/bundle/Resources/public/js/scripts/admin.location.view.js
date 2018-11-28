(function(global, doc, $, React, ReactDOM, eZ, Routing) {
    const listContainers = [...doc.querySelectorAll('.ez-sil')];
    const mfuContainer = doc.querySelector('#ez-mfu');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const sortContainer = doc.querySelector('[data-sort-field][data-sort-order]');
    const sortField = sortContainer.getAttribute('data-sort-field');
    const sortOrder = sortContainer.getAttribute('data-sort-order');
    const mfuAttrs = {
        adminUiConfig: Object.assign({}, eZ.adminUiConfig, {
            token,
            siteaccess,
        }),
        parentInfo: {
            contentTypeIdentifier: mfuContainer.dataset.parentContentTypeIdentifier,
            contentTypeId: parseInt(mfuContainer.dataset.parentContentTypeId, 10),
            locationPath: mfuContainer.dataset.parentLocationPath,
            language: mfuContainer.dataset.parentContentLanguage,
        },
    };
    const handleEditItem = (content) => {
        const contentId = content._id;
        const checkVersionDraftLink = Routing.generate('ezplatform.version_draft.has_no_conflict', { contentId });
        const submitVersionEditForm = () => {
            doc.querySelector('#form_subitems_content_edit_content_info').value = contentId;
            doc.querySelector('#form_subitems_content_edit_version_info_content_info').value = contentId;
            doc.querySelector('#form_subitems_content_edit_version_info_version_no').value =
                content.CurrentVersion.Version.VersionInfo.versionNo;
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
            const addDraftButton = wrapper.querySelector('.ez-btn--add-draft');
            if (addDraftButton) {
                addDraftButton.addEventListener('click', addDraft, false);
            }
            [...wrapper.querySelectorAll('.ez-btn--prevented')].forEach((btn) =>
                btn.addEventListener('click', (event) => event.preventDefault(), false)
            );
            $('#version-draft-conflict-modal').modal('show');
        };
        fetch(checkVersionDraftLink, {
            credentials: 'same-origin',
        }).then(function(response) {
            // Status 409 means that a draft conflict has occurred and the modal must be displayed.
            // Otherwise we can go to Content Item edit page.
            if (response.status === 409) {
                response.text().then(showModal);
            } else if (response.status === 200) {
                submitVersionEditForm();
            }
        });
    };
    const generateLink = (locationId) => Routing.generate('_ezpublishLocation', { locationId });

    listContainers.forEach((container) => {
        const subItemsList = JSON.parse(container.dataset.items).SubitemsList;
        const items = subItemsList.SubitemsRow.map((item) => ({
            content: item.Content,
            location: item.Location,
        }));
        const contentTypes = JSON.parse(container.dataset.contentTypes).ContentTypeInfoList.ContentType;
        const contentTypesMap = contentTypes.reduce((total, item) => {
            total[item._href] = item;

            return total;
        }, {});
        const udwConfigBulkMoveItems = JSON.parse(container.dataset.udwConfigBulkMoveItems);

        ReactDOM.render(
            React.createElement(eZ.modules.SubItems, {
                handleEditItem,
                generateLink,
                parentLocationId: parseInt(container.dataset.location, 10),
                sortClauses: { [sortField]: sortOrder },
                restInfo: { token, siteaccess },
                extraActions: [
                    {
                        component: eZ.modules.MultiFileUpload,
                        attrs: Object.assign({}, mfuAttrs, {
                            onPopupClose: (itemsUploaded) => itemsUploaded.length && global.location.reload(true),
                            contentCreatePermissionsConfig: JSON.parse(container.dataset.mfuCanCreate),
                        }),
                    },
                ],
                items,
                contentTypesMap,
                totalCount: subItemsList.ChildrenCount,
                udwConfigBulkMoveItems,
            }),
            container
        );
    });
})(window, window.document, window.jQuery, window.React, window.ReactDOM, window.eZ, window.Routing);
