(function(global, doc, localStorage, bootstrap, React, ReactDOM, eZ, Routing, Translator) {
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
    const handleEditItem = (content, location) => {
        const contentId = content._id;
        const locationId = location._id;
        const languageCode = content.mainLanguageCode;
        const checkVersionDraftLink = Routing.generate('ezplatform.version_draft.has_no_conflict', { contentId, languageCode, locationId });
        const submitVersionEditForm = () => {
            doc.querySelector('#form_subitems_content_edit_content_info').value = contentId;
            doc.querySelector(`#form_subitems_content_edit_language_${languageCode}`).checked = true;
            doc.querySelector('#form_subitems_content_edit_create').click();
        };
        const addDraft = () => {
            submitVersionEditForm();
            bootstrap.Modal.getOrCreateInstance(doc.querySelector('#version-draft-conflict-modal')).hide();
        };
        const attachModalListeners = (wrapper) => {
            const addDraftButton = wrapper.querySelector('.ibexa-btn--add-draft');
            const conflictModal = doc.querySelector('#version-draft-conflict-modal');

            if (addDraftButton) {
                addDraftButton.addEventListener('click', addDraft, false);
            }

            wrapper
                .querySelectorAll('.ibexa-btn--prevented')
                .forEach((btn) => btn.addEventListener('click', (event) => event.preventDefault(), false));

            if (conflictModal) {
                bootstrap.Modal.getOrCreateInstance(conflictModal).show();
                conflictModal.addEventListener('shown.bs.modal', () => eZ.helpers.tooltips.parse());
            }
        };
        const showModal = (modalHtml) => {
            const wrapper = doc.querySelector('.ez-modal-wrapper');

            wrapper.innerHTML = modalHtml;
            attachModalListeners(wrapper);
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
    const generateLink = (locationId, contentId) => Routing.generate('_ez_content_view', { contentId, locationId });
    const setModalTableTitle = (title) => {
        const modalTableTitleNode = doc.querySelector(`${SELECTOR_MODAL_BULK_ACTION_FAIL} .ibexa-table-header__headline`);

        modalTableTitleNode.innerHTML = title;
        modalTableTitleNode.setAttribute('title', title);
        modalTableTitleNode.dataset.originalTitle = title;
    };
    const setModalTableBody = (failedItemsData) => {
        const modal = doc.querySelector(SELECTOR_MODAL_BULK_ACTION_FAIL);
        const table = modal.querySelector('.ez-bulk-action-failed-modal__table');
        const tableBody = table.querySelector('.ez-bulk-action-failed-modal__table-body');
        const { rowTemplate } = table.dataset;
        const fragment = doc.createDocumentFragment();

        failedItemsData.forEach(({ contentName, contentTypeName }) => {
            const container = doc.createElement('tbody');
            const renderedItem = rowTemplate
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

        bootstrap.Modal.getOrCreateInstance(doc.querySelector(SELECTOR_MODAL_BULK_ACTION_FAIL)).show();
    };
    const getLocationActiveView = (parentLocationId) => {
        const mediaLocationId = eZ.adminUiConfig.locations.media;
        const defaultActiveView = parentLocationId === mediaLocationId ? 'grid' : 'table';
        const activeView = localStorage.getItem(`ez-subitems-active-view-location-${parentLocationId}`);

        return activeView || defaultActiveView;
    };

    listContainers.forEach((container) => {
        const parentLocationId = parseInt(container.dataset.location, 10);
        const activeView = getLocationActiveView(parentLocationId);
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
                activeView,
                parentLocationId,
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
})(
    window,
    window.document,
    window.localStorage,
    window.bootstrap,
    window.React,
    window.ReactDOM,
    window.eZ,
    window.Routing,
    window.Translator
);
