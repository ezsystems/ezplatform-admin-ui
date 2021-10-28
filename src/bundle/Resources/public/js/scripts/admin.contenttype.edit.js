(function (global, doc, eZ, Routing, Translator) {
    const TIMEOUT_REMOVE_PLACEHOLDERS = 1500;
    let targetContainer = null;
    let sourceContainer = null;
    let currentDraggedItem = null;
    let draggedItemPosition = null;
    const draggableGroups = [];
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const sectionsNode = doc.querySelector('.ibexa-content-type-edit__sections');
    const filterFieldInput = doc.querySelector('.ibexa-available-field-types__sidebar-filter');
    const popupMenuElement = sectionsNode.querySelector('.ibexa-popup-menu');
    const addGroupTriggerBtn = sectionsNode.querySelector('.ibexa-content-type-edit__add-field-definitions-group-btn');
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
        triggerElement: addGroupTriggerBtn,
        onItemClick: (event) => {
            const { relatedCollapseSelector } = event.currentTarget.dataset;

            doc.querySelector(relatedCollapseSelector).classList.remove('ibexa-collapse--hidden');
            afterChangeGroup();
        },
    });
    const searchField = (event) => {
        const fieldFilterQueryLowerCase = event.currentTarget.value.toLowerCase();
        const fields = doc.querySelectorAll('.ibexa-available-field-types__list .ibexa-available-field-type');

        fields.forEach((field) => {
            const fieldNameNode = field.querySelector('.ibexa-available-field-type__label');
            const fieldNameLowerCase = fieldNameNode.innerText.toLowerCase();
            const isFieldHidden = !fieldNameLowerCase.includes(fieldFilterQueryLowerCase);

            field.classList.toggle('ibexa-available-field-type--hidden', isFieldHidden);
        });
    };
    const removeDragPlaceholders = () => {
        const placeholderNodes = doc.querySelectorAll(
            '.ibexa-field-definitions-placeholder:not(.ibexa-field-definitions-placeholder--anchored)'
        );

        placeholderNodes.forEach((placeholderNode) => placeholderNode.remove());
    };
    const insertFieldDefinitionNode = (fieldNode) => {
        let targetPlace = '';
        const groupCollapseNode = targetContainer.closest('.ibexa-collapse--field-definitions-group');
        const { fieldsGroupId } = groupCollapseNode.dataset;
        const items = targetContainer.querySelectorAll('.ibexa-collapse');

        if (typeof fieldNode === 'string') {
            const container = doc.createElement('div');

            container.insertAdjacentHTML('beforeend', fieldNode);
            fieldNode = container.querySelector('.ibexa-collapse');
        }

        if (draggedItemPosition === -1) {
            targetPlace = targetContainer.querySelector('.ibexa-field-definitions-placeholder--anchored');
        } else if (draggedItemPosition === 0) {
            targetPlace = targetContainer.firstChild;
        } else {
            targetPlace = [...items].find((item, index) => index === draggedItemPosition);
        }

        const fieldGroupInput =  fieldNode.querySelector('.ibexa-input--field-group');
        const removeFieldsBtn = fieldNode.querySelectorAll('.ibexa-collapse__extra-action-button--remove-field-definitions');

        removeDragPlaceholders();
        fieldGroupInput.value = fieldsGroupId;
        targetContainer.insertBefore(fieldNode, targetPlace);
        removeFieldsBtn.forEach((removeFieldBtn) => {
            removeFieldBtn.addEventListener('click', removeField, false);
        });

        doc.body.dispatchEvent(
            new CustomEvent('ibexa-drop-field-definition', {
                detail: { nodes: [fieldNode] },
            })
        );

        const dropdowns = fieldNode.querySelectorAll('.ibexa-dropdown');

        dropdowns.forEach((dropdownContainer) => {
            const dropdown = new eZ.core.Dropdown({
                container: dropdownContainer,
            });

            dropdown.init();
        });

        draggableGroups.forEach((group) => {
            group.reinit();
        });
    };
    const generateRequest = (action, bodyData, languageCode) => {
        const { actionName, method, contentType } = endpoints[action];
        const { contentTypeGroupId, contentTypeId } = sectionsNode.dataset;
        let endpointURL = `/api/ezp/v2/contenttypegroup/${contentTypeGroupId}/contenttype/${contentTypeId}/${actionName}`;

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
        const submitBtn = doc.querySelector('.ibexa-content-type-edit__publish-content-type');
        const fieldsDefinitionCount = doc.querySelectorAll('.ibexa-collapse--field-definition').length;
        const groups = doc.querySelectorAll('.ibexa-collapse--field-definitions-group');
        const itemsAction = doc.querySelectorAll('.ibexa-content-type-edit__add-field-definitions-group .ibexa-popup-menu__item-content');

        groups.forEach((group) => {
            const groupFieldsDefinitionCount = group.querySelectorAll('.ibexa-collapse--field-definition').length;
            const emptyGroupPlaceholder = group.querySelector('.ibexa-field-definitions-empty-group');
            const anchoredPlaceholder = group.querySelector('.ibexa-field-definitions-placeholder--anchored');

            emptyGroupPlaceholder.classList.toggle('ibexa-field-definitions-empty-group--hidden', groupFieldsDefinitionCount !== 0);
            anchoredPlaceholder.classList.toggle('ibexa-field-definitions-placeholder--hidden', groupFieldsDefinitionCount === 0);
        });

        itemsAction.forEach((itemAction) => {
            const { relatedCollapseSelector } = itemAction.dataset;
            const isGroupHidden = doc.querySelector(relatedCollapseSelector).classList.contains('ibexa-collapse--hidden');

            itemAction.classList.toggle('ibexa-popup-menu__item-content--disabled', !isGroupHidden);
        });

        doc.querySelectorAll('.ibexa-collapse--field-definition').forEach((fieldDefinition, index) => {
            fieldDefinition.querySelector('.ibexa-input--position').value = index;
        });

        submitBtn.toggleAttribute('disabled', !fieldsDefinitionCount);
    };
    const addField = () => {
        if (!sourceContainer.classList.contains('ibexa-available-field-types__list')) {
            insertFieldDefinitionNode(currentDraggedItem);
            afterChangeGroup();

            return;
        }

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

        fetch(generateRequest('add', bodyData, languageCode))
            .then(eZ.helpers.request.getTextFromResponse)
            .then((response) => {
                insertFieldDefinitionNode(response);
                afterChangeGroup();
            })
            .catch(eZ.helpers.notification.showErrorNotification);
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
            .then(() => afterChangeGroup())
            .catch(eZ.helpers.notification.showErrorNotification);
    };
    const removeFieldsGroup = (event) => {
        const collapseNode = event.currentTarget.closest('.ibexa-collapse');
        const fieldsToDelete = [...collapseNode.querySelectorAll('.ibexa-collapse--field-definition')].map(
            (fieldDefinition) => fieldDefinition.dataset.fieldDefinitionIdentifier
        );
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
            .catch(eZ.helpers.notification.showErrorNotification);
    };
    const removeField = (event) => {
        const collapseNode = event.currentTarget.closest('.ibexa-collapse');
        const itemToDeleteIdentifiers = collapseNode.dataset.fieldDefinitionIdentifier;
        const bodyData = {
            FieldDefinitionDelete: {
                fieldDefinitionIdentifiers: [itemToDeleteIdentifiers],
            },
        };

        fetch(generateRequest('remove', bodyData))
            .then(eZ.helpers.request.getTextFromResponse)
            .then(() => {
                collapseNode.remove();
                afterChangeGroup();
            })
            .catch(eZ.helpers.notification.showErrorNotification);
    };

    class FieldDefinitionDraggable extends eZ.core.Draggable {
        onDrop(event) {
            targetContainer = event.currentTarget;

            const dragContainerItems = targetContainer.querySelectorAll(
                '.ibexa-collapse--field-definition, .ibexa-field-definitions-placeholder:not(.ibexa-field-definitions-placeholder--anchored)'
            );

            draggedItemPosition = [...dragContainerItems].findIndex((item, index, array) => {
                return item.classList.contains('ibexa-field-definitions-placeholder') && index < array.length - 1;
            });

            if (sourceContainer.isEqualNode(targetContainer)) {
                reorderFields();
            } else {
                addField();
            }

            removeDragPlaceholders();
        }

        onDragStart(event) {
            super.onDragStart(event);

            currentDraggedItem = event.currentTarget;
            sourceContainer = currentDraggedItem.parentNode;
        }

        onDragEnd() {
            currentDraggedItem.style.removeProperty('display');
        }
    }

    filterFieldInput.addEventListener('keyup', searchField, false);

    doc.querySelectorAll('.ibexa-collapse__extra-action-button--remove-field-definitions').forEach((removeFieldDefinitionsButton) => {
        removeFieldDefinitionsButton.addEventListener('click', removeField, false);
    });
    doc.querySelectorAll('.ibexa-collapse__extra-action-button--remove-field-definitions-group').forEach(
        (removeFieldDefinitionsGroupButton) => {
            removeFieldDefinitionsGroupButton.addEventListener('click', removeFieldsGroup, false);
        }
    );

    doc.querySelectorAll('.ibexa-available-field-types__list .ibexa-available-field-type').forEach((availableField) => {
        availableField.addEventListener(
            'dragstart',
            (event) => {
                currentDraggedItem = event.currentTarget;
                sourceContainer = currentDraggedItem.parentNode;
                currentDraggedItem.classList.add('ibexa-available-field-type--is-dragging-out');
            },
            false
        );
        availableField.addEventListener(
            'dragend',
            () => {
                currentDraggedItem.classList.remove('ibexa-available-field-type--is-dragging-out');
            },
            false
        );
    });
    doc.querySelectorAll('.ibexa-content-type-edit__field-definition-drop-zone').forEach((collapseCotentNode) => {
        const draggable = new FieldDefinitionDraggable({
            itemsContainer: collapseCotentNode,
            selectorItem: '.ibexa-collapse--field-definition',
            timeoutRemovePlaceholders: TIMEOUT_REMOVE_PLACEHOLDERS,
            selectorPlaceholder: '.ibexa-field-definitions-placeholder',
        });

        draggable.init();
        draggableGroups.push(draggable);
    });

    doc.querySelector('.ibexa-btn--save-content-type').addEventListener(
        'click',
        () => {
            if (doc.querySelectorAll('.ibexa-collapse--field-definition').length) {
                return;
            }

            eZ.helpers.notification.showErrorNotification(
                Translator.trans(
                    /*@Desc("You have to add at least one field definition")*/ 'content_type.edit.error.no_added_fields_definition',
                    {},
                    'content_type'
                )
            );
        },
        false
    );
})(window, window.document, window.eZ, window.Routing, window.Translator);
