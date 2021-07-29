(function (global, doc, eZ) {
    const searchFiledInput = doc.querySelector('.ibexa-field-types-available__search');
    const popupMenuElement = doc.querySelector('.ibexa-content-type-edit__sections .ibexa-popup-menu');
    const triggerElement = doc.querySelector(
        '.ibexa-content-type-edit__sections .ibexa-content-type-edit__add-field-definition-group-button'
    );
    const popupMenu = new eZ.core.PopupMenu({
        popupMenuElement,
        triggerElement,
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
    const removeFieldsGroup = (event) => {
        const collapseNode = event.currentTarget.closest('.ibexa-collapse');
        const fieldDefinitionGroupNode = collapseNode.querySelector('.ibexa-content-type-edit__field-definition-group');
        const { emptyGroupTemplate } = collapseNode.dataset;

        collapseNode.classList.add('ibexa-collapse--hidden');
        fieldDefinitionGroupNode.innerHTML = emptyGroupTemplate;
        fieldDefinitionGroupNode.classList.add('ibexa-content-type-edit__field-definition-group--empty');
    }
    const removeField = (event) => {
        const collapseNode = event.currentTarget.closest('.ibexa-collapse');
        const parentCollapseNode = collapseNode.closest('.ibexa-collapse--field-definitions-group');
        const parentFieldDefinitionGroupNode = parentCollapseNode.querySelector('.ibexa-content-type-edit__field-definition-group');
        const { emptyGroupTemplate } = parentCollapseNode.dataset;

        collapseNode.remove();

        const numberOfFields = parentCollapseNode.querySelectorAll('.ibexa-collapse--field-definition').length;

        if (numberOfFields === 0) {
            parentFieldDefinitionGroupNode.innerHTML = emptyGroupTemplate;
            parentFieldDefinitionGroupNode.classList.add('ibexa-content-type-edit__field-definition-group--empty');
        }
    }

    searchFiledInput.addEventListener('keyup', searchField, false);

    doc.querySelectorAll('.ibexa-collapse__extra-action-button--remove-field').forEach(removeFieldButton => {
        removeFieldButton.addEventListener('click', removeField, false);
    })
    doc.querySelectorAll('.ibexa-collapse__extra-action-button--remove-fields-group').forEach(removeFieldGroupButton => {
        removeFieldGroupButton.addEventListener('click', removeFieldsGroup, false);
    })
})(window, window.document, window.eZ);
