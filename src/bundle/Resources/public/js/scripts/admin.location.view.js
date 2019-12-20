(function(global, doc, $, React, ReactDOM, eZ, Routing, Translator) {
    const SELECTOR_MODAL_BULK_ACTION_FAIL = '#bulk-action-failed-modal';
    const listContainers = doc.querySelectorAll('.ez-sil');
    const mfuContainer = doc.querySelector('#ez-mfu');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const sortContainer = doc.querySelector('[data-sort-field][data-sort-order]');
    const sortField = sortContainer.getAttribute('data-sort-field');
    const sortOrder = sortContainer.getAttribute('data-sort-order');
    const mfuAttrs = {
        adminUiConfig: {
            ...eZ.adminUiConfig,
            token,
            siteaccess,
        },
        parentInfo: {
            contentTypeIdentifier: mfuContainer.dataset.parentContentTypeIdentifier,
            contentTypeId: parseInt(mfuContainer.dataset.parentContentTypeId, 10),
            locationPath: mfuContainer.dataset.parentLocationPath,
            language: mfuContainer.dataset.parentContentLanguage,
        },
        currentLanguage: mfuContainer.dataset.currentLanguage,
    };
    const handleEditItem = (content) => {
        const contentId = content._id;
        const languageCode = content.mainLanguageCode;
        const checkVersionDraftLink = Routing.generate('ezplatform.version_draft.has_no_conflict', { contentId, languageCode });
        const submitVersionEditForm = () => {
            doc.querySelector('#form_subitems_content_edit_content_info').value = contentId;
            doc.querySelector(`#form_subitems_content_edit_language_${languageCode}`).checked = true;
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
            wrapper
                .querySelectorAll('.ez-btn--prevented')
                .forEach((btn) => btn.addEventListener('click', (event) => event.preventDefault(), false));
            $('#version-draft-conflict-modal').modal('show');
        };
        const checkEditPermissionLink = Routing.generate('ezplatform.content.check_edit_permission', {
            contentId,
            languageCode: content.mainLanguageCode,
        });
        const errorMessage = Translator.trans(
            /*@Desc("You don't have permission to edit this Content item")*/ 'content.edit.permission.error',
            {},
            'content'
        );
        const handleCanEditCheck = (response) => {
            if (response.canEdit) {
                return fetch(checkVersionDraftLink, { mode: 'same-origin', credentials: 'same-origin' });
            }

            throw new Error(errorMessage);
        };

        fetch(checkEditPermissionLink, { mode: 'same-origin', credentials: 'same-origin' })
            .then(eZ.helpers.request.getJsonFromResponse)
            .then(handleCanEditCheck)
            .then((response) => {
                // Status 409 means that a draft conflict has occurred and the modal must be displayed.
                // Otherwise we can go to Content Item edit page.
                if (response.status === 409) {
                    response.text().then(showModal);
                } else if (response.status === 200) {
                    submitVersionEditForm();
                }
            })
            .catch(eZ.helpers.notification.showErrorNotification);
    };
    const generateLink = (locationId) => Routing.generate('_ezpublishLocation', { locationId });
    const setModalTableTitle = (title) => {
        const modalTableTitleNode = doc.querySelector(`${SELECTOR_MODAL_BULK_ACTION_FAIL} .ez-table-header__headline`);

        modalTableTitleNode.innerHTML = title;
    };
    const setModalTableBody = (failedItemsData) => {
        const modal = doc.querySelector(SELECTOR_MODAL_BULK_ACTION_FAIL);
        const table = modal.querySelector('.ez-bulk-action-failed-modal__table');
        const tableBody = table.querySelector('.ez-bulk-action-failed-modal__table-body');
        const tableRowTemplate = table.dataset.tableRowTemplate;
        const fragment = doc.createDocumentFragment();

        failedItemsData.forEach(({ contentName, contentTypeName }) => {
            const container = doc.createElement('tbody');
            const renderedItem = tableRowTemplate
                .replace('{{ content_name }}', contentName)
                .replace('{{ content_type_name }}', contentTypeName);

            container.insertAdjacentHTML('beforeend', renderedItem);

            const tableRowNode = container.querySelector('tr');

            fragment.append(tableRowNode);
        });

        removeNodeChildren(tableBody);
        tableBody.append(fragment);
    };
    const removeNodeChildren = (node) => {
        while (node.firstChild) {
            node.removeChild(node.firstChild);
        }
    };
    const showBulkActionFailedModal = (tableTitle, failedItemsData) => {
        setModalTableBody(failedItemsData);
        setModalTableTitle(tableTitle);

        $(SELECTOR_MODAL_BULK_ACTION_FAIL).modal('show');
    };

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
        const udwConfigBulkAddLocation = JSON.parse(container.dataset.udwConfigBulkAddLocation);
        const mfuContentTypesMap = Object.values(eZ.adminUiConfig.contentTypes).reduce((contentTypeDataMap, contentTypeGroup) => {
            for (const contentTypeData of contentTypeGroup) {
                contentTypeDataMap[contentTypeData.href] = contentTypeData;
            }

            return contentTypeDataMap;
        }, {});

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
                        attrs: {
                            ...mfuAttrs,
                            onPopupClose: (itemsUploaded) => itemsUploaded.length && global.location.reload(true),
                            contentCreatePermissionsConfig: JSON.parse(container.dataset.mfuCreatePermissionsConfig),
                            contentTypesMap: mfuContentTypesMap,
                        },
                    },
                ],
                items,
                contentTypesMap,
                totalCount: subItemsList.ChildrenCount,
                udwConfigBulkMoveItems,
                udwConfigBulkAddLocation,
                showBulkActionFailedModal,
            }),
            container
        );
    });
})(window, window.document, window.jQuery, window.React, window.ReactDOM, window.eZ, window.Routing, window.Translator);
