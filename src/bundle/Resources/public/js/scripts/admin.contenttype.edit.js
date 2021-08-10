(function (global, doc, eZ, Routing) {
    let targetContainer = null;
    let sourceContainer = null;
    let currentDraggedItem = null;
    let draggedItemPosition = null;
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
    const sectionsNode = doc.querySelector('.ibexa-content-type-edit__sections');
    const searchFiledInput = doc.querySelector('.ibexa-field-types-available__search');
    const popupMenuElement = doc.querySelector('.ibexa-content-type-edit__sections .ibexa-popup-menu');
    const triggerElement = doc.querySelector(
        '.ibexa-content-type-edit__sections .ibexa-content-type-edit__add-field-definition-group-button'
    );
    const endpoints = {
        add: {
            actionName: 'add_field_definition',
            method: 'POST',
            contentType: 'application/vnd.ez.api.ContentTypFieldDefinitionCreate+json',
        },
        remove: {
            actionName: 'remove_field_definition',
            method: 'DELETE',
            contentType: 'application/vnd.ez.api.ContentTypeFieldDefinitionDelete+json',
        },
        reorder: {
            actionName: 'reorder_field_definitions',
            method: 'PUT',
            contentType: 'application/vnd.ez.api.ContentTypeFieldDefinitionReorder+json',
        },
    };
    const popupMenu = new eZ.core.PopupMenu({
        popupMenuElement,
        triggerElement,
        onItemClick: (event) => {
            const { relatedCollapseSelector } = event.currentTarget.dataset;

            doc.querySelector(relatedCollapseSelector).classList.remove('ibexa-collapse--hidden');
        },
    });
    const popupItemsToGenerate = [...sectionsNode.querySelectorAll('.ibexa-collapse--field-definitions-group')].map(
        (fieldDefinitionsGroup) => {
            const { fieldGroupId } = fieldDefinitionsGroup.dataset;
            const label = fieldDefinitionsGroup.querySelector('.ibexa-collapse__header-label').textContent;
            const relatedCollapseSelector = `.ibexa-field-definitions-group-${fieldGroupId}`;

            return {
                label,
                relatedCollapseSelector,
            };
        }
    );
    const searchField = (event) => {
        const fieldFilterQueryLowerCase = event.currentTarget.value.toLowerCase();

        doc.querySelectorAll('.ibexa-field-types-available__fields .ibexa-field-types-available__field').forEach((field) => {
            const fieldNameNode = field.querySelector('.ibexa-field-types-available__field-name');
            const fieldNameLowerCase = fieldNameNode.innerText.toLowerCase();
            const fieldIsHidden = !fieldNameLowerCase.includes(fieldFilterQueryLowerCase);

            field.classList.toggle('ibexa-field-types-available__field--hidden', fieldIsHidden);
        });
    };
    const insertFieldDefinitionNode = (fieldNode) => {
        let targetPlace = '';
        const groupCollaspeNode = targetContainer.closest('.ibexa-collapse--field-definitions-group');
        const { fieldGroupId } = groupCollaspeNode.dataset;

        if (typeof fieldNode === 'string') {
            const container = doc.createElement('div');

            container.insertAdjacentHTML('beforeend', fieldNode);
            fieldNode = container.querySelector('.ibexa-collapse');
        }

        if (draggedItemPosition === -1) {
            targetPlace = targetContainer.lastChild;
        } else if (draggedItemPosition === 0) {
            targetPlace = targetContainer.firstChild;
        } else {
            targetPlace = targetContainer.querySelector(`.ibexa-collapse:nth-child(${draggedItemPosition})`).nextSibling;
        }

        fieldNode.querySelector('.ibexa-input--field-group').value = fieldGroupId;
        targetContainer.insertBefore(fieldNode, targetPlace);

        fieldNode.querySelectorAll('.ibexa-collapse__extra-action-button--remove-field').forEach((removeFieldButton) => {
            removeFieldButton.addEventListener('click', removeField, false);
        });

        if (fieldNode.dataset.identifierValue) {
            doc.body.dispatchEvent(
                new CustomEvent('ibexa-drop-field-definition', {
                    detail: { fieldSelector: `.ibexa-field-definition-${fieldNode.dataset.identifierValue}` },
                })
            );
        }
    };
    const generateRequest = (action, bodyData, languageCode) => {
        const { actionName, method, contentType } = endpoints[action];
        const { contentTypeGroupId, contentTypeId } = sectionsNode.dataset;
        const baseURL = '/api/ezp/v2/contenttypegroup/_contentTypeGroupId_/contenttype/_contentTypeId_/_actionName_';
        let endpointURL = baseURL
            .replace('_contentTypeGroupId_', contentTypeGroupId)
            .replace('_contentTypeId_', contentTypeId)
            .replace('_actionName_', actionName);

        if (languageCode) {
            endpointURL += `/${languageCode}`;
        }

        return new Request(endpointURL, {
            method,
            mode: 'same-origin',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/html',
                'Content-Type': contentType,
                'X-Siteaccess': siteaccess,
                'X-CSRF-Token': token,
            },
            body: JSON.stringify(bodyData),
        });
    };
    const changeSubmitButtonAttributes = () => {
        const submitButton = doc.querySelector('.ibexa-content-type-edit__publish-content-type');
        const definedFields = doc.querySelectorAll('.ibexa-collapse--field-definition');

        if (definedFields.length) {
            submitButton.removeAttribute('disabled');
        } else {
            submitButton.setAttribute('disabled', 'disabled');
        }
    };
    const addField = () => {
        const { languageCode } = sectionsNode.dataset;
        const { itemIdentifier } = currentDraggedItem.dataset;
        const bodyData = {
            FieldDefinitionCreate: {
                fieldTypeIdentifier: itemIdentifier,
            },
        };

        if (draggedItemPosition !== -1) {
            bodyData.FieldDefinitionCreate.position = draggedItemPosition;
        }

        if (!sourceContainer.classList.contains('ibexa-field-types-available__fields')) {
            insertFieldDefinitionNode(currentDraggedItem);

            return;
        }

        const request = generateRequest('add', bodyData, languageCode);

        fetch(request)
            .then(eZ.helpers.request.getTextFromResponse)
            .then((response) => {
                if (targetContainer.classList.contains('ibexa-content-type-edit__field-definition-group--empty')) {
                    targetContainer.classList.remove('ibexa-content-type-edit__field-definition-group--empty');
                    targetContainer.innerHTML = '';
                }

                insertFieldDefinitionNode(response);
                changeSubmitButtonAttributes();
            })
            .catch((errorMessage) => eZ.helpers.notification.showErrorNotification(errorMessage));
    };
    const reorderFields = () => {
        insertFieldDefinitionNode(currentDraggedItem);

        const fieldsOrder = [...doc.querySelectorAll('.ibexa-collapse--field-definition')].map(
            (fieldDefinition) => fieldDefinition.dataset.fieldDefinitionIdentifier
        );
        const bodyData = {
            FieldDefinitionOrder: {
                fieldDefinitionIdentifiers: fieldsOrder,
            },
        };
        const request = generateRequest('reorder', bodyData);

        fetch(request)
            .then(eZ.helpers.request.getTextFromResponse)
            .then((response) => {
                console.log(response);
            })
            .catch((errorMessage) => eZ.helpers.notification.showErrorNotification(errorMessage));
    };
    const removeFieldsGroup = (event) => {
        const collapseNode = event.currentTarget.closest('.ibexa-collapse');
        const fieldDefinitionGroupNode = collapseNode.querySelector('.ibexa-content-type-edit__field-definition-group');
        const { emptyGroupTemplate } = collapseNode.dataset;
        const fieldsToDelete = [...fieldDefinitionGroupNode.querySelectorAll('.ibexa-collapse--field-definition')].map(
            (fieldDefinition) => fieldDefinition.dataset.fieldDefinitionIdentifier
        );
        const bodyData = {
            FieldDefinitionDelete: {
                fieldDefinitionIdentifiers: fieldsToDelete,
            },
        };
        const request = generateRequest('remove', bodyData);

        fetch(request)
            .then(eZ.helpers.request.getTextFromResponse)
            .then((response) => {
                collapseNode.classList.add('ibexa-collapse--hidden');
                fieldDefinitionGroupNode.innerHTML = emptyGroupTemplate;
                fieldDefinitionGroupNode.classList.add('ibexa-content-type-edit__field-definition-group--empty');
                changeSubmitButtonAttributes();
            })
            .catch((errorMessage) => eZ.helpers.notification.showErrorNotification(errorMessage));
    };
    const removeField = (event) => {
        const collapseNode = event.currentTarget.closest('.ibexa-collapse');
        const parentCollapseNode = collapseNode.closest('.ibexa-collapse--field-definitions-group');
        const parentFieldDefinitionGroupNode = parentCollapseNode.querySelector('.ibexa-content-type-edit__field-definition-group');
        const { emptyGroupTemplate } = parentCollapseNode.dataset;
        const itemIdentifierToDelete = collapseNode.dataset.fieldDefinitionIdentifier;

        const bodyData = {
            FieldDefinitionDelete: {
                fieldDefinitionIdentifiers: [itemIdentifierToDelete],
            },
        };

        const request = generateRequest('remove', bodyData);

        fetch(request)
            .then(eZ.helpers.request.getTextFromResponse)
            .then((response) => {
                collapseNode.remove();

                const numberOfFields = parentCollapseNode.querySelectorAll('.ibexa-collapse--field-definition').length;

                if (numberOfFields === 0) {
                    parentFieldDefinitionGroupNode.innerHTML = emptyGroupTemplate;
                    parentFieldDefinitionGroupNode.classList.add('ibexa-content-type-edit__field-definition-group--empty');
                }

                changeSubmitButtonAttributes();
            })
            .catch((errorMessage) => eZ.helpers.notification.showErrorNotification(errorMessage));
    };
    class FieldDefinitionDraggable extends eZ.core.Draggable {
        onDrop(event) {
            targetContainer = event.currentTarget;

            const dragContainerItems = targetContainer.querySelectorAll(
                '.ibexa-collapse--field-definition, .ibexa-content-type-edit__field-definition-placeholder'
            );

            draggedItemPosition = [...dragContainerItems].findIndex((item, index, array) => {
                return item.classList.contains('ibexa-content-type-edit__field-definition-placeholder') && index < array.length - 1;
            });

            if (sourceContainer.isEqualNode(targetContainer)) {
                reorderFields();
            } else {
                addField();
            }

            super.removePlaceholder();
        }

        onDragStart(event) {
            super.onDragStart(event);

            currentDraggedItem = event.currentTarget;
            sourceContainer = currentDraggedItem.parentNode;
        }

        removePlaceholderAfterTimeout() {}
    }

    popupMenu.generateItems(popupItemsToGenerate, (itemAction, item) => {
        const reletedCollapse = doc.querySelector(item.relatedCollapseSelector);
        const isButtonDiabled = !reletedCollapse.classList.contains('ibexa-collapse--hidden');

        itemAction.dataset.relatedCollapseSelector = item.relatedCollapseSelector;
        itemAction.toggleAttribute('disabled', isButtonDiabled);
        itemAction.classList.toggle('ibexa-popup-menu__item-action--disabled', isButtonDiabled);
    });

    searchFiledInput.addEventListener('keyup', searchField, false);

    doc.querySelectorAll('.ibexa-collapse__extra-action-button--remove-field').forEach((removeFieldButton) => {
        removeFieldButton.addEventListener('click', removeField, false);
    });
    doc.querySelectorAll('.ibexa-collapse__extra-action-button--remove-fields-group').forEach((removeFieldGroupButton) => {
        removeFieldGroupButton.addEventListener('click', removeFieldsGroup, false);
    });

    doc.querySelectorAll('.ibexa-field-types-available__fields .ibexa-field-types-available__field').forEach((availableItem) => {
        availableItem.addEventListener(
            'dragstart',
            (event) => {
                currentDraggedItem = event.currentTarget;
                sourceContainer = currentDraggedItem.parentNode;
            },
            false
        );
    });
    doc.querySelectorAll('.ibexa-content-type-edit__field-definition-group').forEach((collapseCotentNode) => {
        const draggable = new FieldDefinitionDraggable({
            itemsContainer: collapseCotentNode,
            selectorItem: '.ibexa-collapse--field-definition',
            selectorPlaceholder: '.ibexa-content-type-edit__field-definition-placeholder',
        });

        draggable.init();
    });
})(window, window.document, window.eZ, window.Routing);
