(function (global, doc, eZ) {
    const searchFiledInput = doc.querySelector('.ibexa-field-types-available__search');
    const popupMenuElement = doc.querySelector('.ibexa-content-type-edit-sections .ibexa-popup-menu');
    const triggerElement = doc.querySelector(
        '.ibexa-content-type-edit-sections .ibexa-content-type-edit__add-field-definition-group-button'
    );
    const popupMenu = new eZ.core.PopupMenu({
        popupMenuElement,
        triggerElement,
        onItemClick: (event) => {
            console.log('xxxxx');
        },
    });
    const searchField = (event) => {
        const fieldFilterQueryLowerCase = event.currentTarget.value.toLowerCase();

        doc.querySelectorAll('.ibexa-field-types-available__fields .ibexa-field-types-available__field').forEach((field) => {
            const fieldNameNode = field.querySelector('.ibexa-field-types-available__field-name');
            const fieldNameLowerCase = fieldNameNode.innerText.toLowerCase();
            const fieldIsHidden = !fieldNameLowerCase.includes(fieldFilterQueryLowerCase);

            console.log(fieldIsHidden);

            field.classList.toggle('ibexa-field-types-available__field--hidden', fieldIsHidden);
        });
    };

    searchFiledInput.addEventListener('keyup', searchField, false);
})(window, window.document, window.eZ);
