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
    const removeFieldsGroup = () => {
        console.log('remove group')
    }
    const removeField = () => {
        console.log('remove field')
    }

    searchFiledInput.addEventListener('keyup', searchField, false);

    doc.querySelectorAll('.ibexa-collapse__extra-action-button--remove-field').forEach(removeFieldButton => {
        removeFieldButton.addEventListener('click', removeField, false);
    })
    doc.querySelectorAll('.ibexa-collapse__extra-action-button--remove-fields-group').forEach(removeFieldGroupButton => {
        removeFieldGroupButton.addEventListener('click', removeFieldsGroup, false);
    })
})(window, window.document, window.eZ);
