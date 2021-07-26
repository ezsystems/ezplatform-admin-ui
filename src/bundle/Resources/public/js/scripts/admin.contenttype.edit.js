(function (global, doc) {
    const AVAILABLE_FIELDS_HEIGHT_OFFSET = 150;
    const contentNode = doc.querySelector('.ibexa-edit-content');
    const availableFieldsNode = doc.querySelector('.ibexa-field-types-available');
    const searchFiledInput = doc.querySelector('.ibexa-field-types-available__search');
    const searchField = (event) => {
        const fieldFilterQueryLowerCase = event.currentTarget.value.toLowerCase();

        doc.querySelectorAll('.ibexa-field-types-available__fields .ibexa-field-types-available__field').forEach((field) => {
            const fieldNameNode = field.querySelector('.ibexa-field-types-available__field-name');
            const fieldNameLowerCase = fieldNameNode.innerText.toLowerCase();
            const fieldIsHidden = !fieldNameLowerCase.includes(fieldFilterQueryLowerCase);

            field.classList.toggle('.ibexa-field-types-available__field--hidden', fieldIsHidden);
        });
    };
    // fitAvailableFieldsContainer = () => {
    //     const { height: contentHeight } = contentNode.getBoundingClientRect();

    //     availableFieldsNode.style.height = `${contentHeight - AVAILABLE_FIELDS_HEIGHT_OFFSET}px`;
    // };

    searchFiledInput.addEventListener('keyup', searchField, false);
    // contentNode.addEventListener('scroll', fitAvailableFieldsContainer, false);
})(window, window.document);
