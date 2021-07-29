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
        '.ibexa-content-type-edit__sections .ibexa-content-type-edit__add-field-definitions-group-button'
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
            afterChangeGroup();
        },
    });
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
        const { fieldsGroupId } = groupCollaspeNode.dataset;

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

        fieldNode.querySelector('.ibexa-input--field-group').value = fieldsGroupId;
        targetContainer.insertBefore(fieldNode, targetPlace);
        fieldNode.querySelectorAll('.ibexa-collapse__extra-action-button--remove-field-definitions').forEach((removeFieldButton) => {
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
    const afterChangeGroup = () => {
        const submitButton = doc.querySelector('.ibexa-content-type-edit__publish-content-type');
        const formHasAnyFieldDefinition = doc.querySelectorAll('.ibexa-collapse--field-definition').length;

        doc.querySelectorAll('.ibexa-collapse--field-definitions-group').forEach((group) => {
            const fields = group.querySelectorAll('.ibexa-collapse--field-definition');
            const numberOfFields = fields.length;
            const emptyGroupPlaceholder = group.querySelector('.ibexa-content-type-edit__empty-group-placeholder');

            emptyGroupPlaceholder.classList.toggle('ibexa-content-type-edit__empty-group-placeholder--hidden', numberOfFields !== 0);
        });

        doc.querySelectorAll('.ibexa-content-type-edit__add-field-definitions-group .ibexa-popup-menu__item-action').forEach(
            (itemAction) => {
                const { relatedCollapseSelector } = itemAction.dataset;
                const isGroupHidden = doc.querySelector(relatedCollapseSelector).classList.contains('ibexa-collapse--hidden');

                itemAction.classList.toggle('ibexa-popup-menu__item-action--disabled', !isGroupHidden);
            }
        );

        doc.querySelectorAll('.ibexa-collapse--field-definition').forEach((fieldDefinition, index) => {
            fieldDefinition.querySelector('.ibexa-input--position').value = index;
        });

        if (formHasAnyFieldDefinition) {
            submitButton.removeAttribute('disabled');
        } else {
            submitButton.setAttribute('disabled', 'disabled');
        }
    };
    const addField = () => {
        const { languageCode } = sectionsNode.dataset;
        const { itemIdentifier } = currentDraggedItem.dataset;
        const { fieldsGroupId } = targetContainer.closest('.ibexa-collapse--field-definitions-group').dataset;

        const bodyData = {
            FieldDefinitionCreate: {
                fieldTypeIdentifier: itemIdentifier,
                fieldGroupIdentifier: fieldsGroupId,
            },
        };

        if (draggedItemPosition !== -1) {
            bodyData.FieldDefinitionCreate.position = draggedItemPosition;
        }

        if (!sourceContainer.classList.contains('ibexa-field-types-available__fields')) {
            insertFieldDefinitionNode(currentDraggedItem);
            afterChangeGroup();

            return;
        }

        fetch(generateRequest('add', bodyData, languageCode))
            .then(eZ.helpers.request.getTextFromResponse)
            .then((response) => {
                insertFieldDefinitionNode(response);
                afterChangeGroup();
            })
            .catch((errorMessage) => eZ.helpers.notification.showErrorNotification(errorMessage));
    };
    const reorderFields = () => {
        insertFieldDefinitionNode(currentDraggedItem);

        const fieldsOrder = [...doc.querySelectorAll('.ibexa-collapse--field-definition')].map(
            (fieldDefinition) => fieldDefinition.dataset.fieldDefinitionIdentifier
        );
        const bodyData = {
            FieldDefinitionReorder: {
                fieldDefinitionIdentifiers: fieldsOrder,
            },
        };
        const request = generateRequest('reorder', bodyData);

        fetch(request)
            .then(eZ.helpers.request.getTextFromResponse)
            .then(afterChangeGroup)
            .catch((errorMessage) => eZ.helpers.notification.showErrorNotification(errorMessage));
    };
    const removeFieldsGroup = (event) => {
        const collapseNode = event.currentTarget.closest('.ibexa-collapse');
        const fieldDefinitionGroupNode = collapseNode.querySelector('.ibexa-content-type-edit__field-definitions');
        const fieldsToDelete = [...fieldDefinitionGroupNode.querySelectorAll('.ibexa-collapse--field-definition')].map(
            (fieldDefinition) => fieldDefinition.dataset.fieldDefinitionIdentifier
        );
        console.log(fieldsToDelete);
        const bodyData = {
            FieldDefinitionDelete: {
                fieldDefinitionIdentifiers: fieldsToDelete,
            },
        };

        fetch(generateRequest('remove', bodyData))
            .then(eZ.helpers.request.getTextFromResponse)
            .then(() => {
                collapseNode.classList.add('ibexa-collapse--hidden');
                collapseNode.querySelectorAll('.ibexa-collapse--field-definition').forEach((fieldDefinition) => {
                    fieldDefinition.remove();
                });
                afterChangeGroup();
            })
            .catch((errorMessage) => eZ.helpers.notification.showErrorNotification(errorMessage));
    };
    const removeField = (event) => {
        const collapseNode = event.currentTarget.closest('.ibexa-collapse');
        const itemIdentifierToDelete = collapseNode.dataset.fieldDefinitionIdentifier;
        const bodyData = {
            FieldDefinitionDelete: {
                fieldDefinitionIdentifiers: [itemIdentifierToDelete],
            },
        };

        fetch(generateRequest('remove', bodyData))
            .then(eZ.helpers.request.getTextFromResponse)
            .then(() => {
                collapseNode.remove();
                afterChangeGroup();
            })
            .catch((errorMessage) => eZ.helpers.notification.showErrorNotification(errorMessage));
    };
    class FieldDefinitionDraggable extends eZ.core.Draggable {
        onDrop(event) {
            targetContainer = event.currentTarget;

            const dragContainerItems = targetContainer.querySelectorAll(
                '.ibexa-collapse--field-definition, .ibexa-content-type-edit__field-definitions-placeholder'
            );

            draggedItemPosition = [...dragContainerItems].findIndex((item, index, array) => {
                return item.classList.contains('ibexa-content-type-edit__field-definitions-placeholder') && index < array.length - 1;
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

    searchFiledInput.addEventListener('keyup', searchField, false);

    doc.querySelectorAll('.ibexa-collapse__extra-action-button--remove-field-definitions').forEach((removeFieldDefinitionsButton) => {
        removeFieldDefinitionsButton.addEventListener('click', removeField, false);
    });
    doc.querySelectorAll('.ibexa-collapse__extra-action-button--remove-field-definitions-group').forEach(
        (removeFieldDefinitionsGroupButton) => {
            removeFieldDefinitionsGroupButton.addEventListener('click', removeFieldsGroup, false);
        }
    );

    doc.querySelectorAll('.ibexa-field-types-available__fields .ibexa-field-types-available__field').forEach((availableField) => {
        availableField.addEventListener(
            'dragstart',
            (event) => {
                currentDraggedItem = event.currentTarget;
                sourceContainer = currentDraggedItem.parentNode;
            },
            false
        );
    });
    doc.querySelectorAll('.ibexa-content-type-edit__field-definitions').forEach((collapseCotentNode) => {
        const draggable = new FieldDefinitionDraggable({
            itemsContainer: collapseCotentNode,
            selectorItem: '.ibexa-collapse--field-definition',
            selectorPlaceholder: '.ibexa-content-type-edit__field-definitions-placeholder',
        });

        draggable.init();
    });
})(window, window.document, window.eZ, window.Routing);
