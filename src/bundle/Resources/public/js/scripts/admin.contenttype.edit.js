(function (global, doc) {
    const searchField = (event) => {
        // const blockFilterQueryLowerCase = blockFilterQuery.toLowerCase();
        // const isHidden = !blockNameLowerCase.includes(blockFilterQueryLowerCase);
        const fieldFilterQueryLowerCase = event.currentTarget.value.toLowerCase();

        doc.querySelectorAll('.ibexa-field-types-available__fields .ibexa-field-types-available__field').forEach((field) => {
            const fieldNameNode = field.querySelector('.ibexa-field-types-available__field-name');
            const fieldNameLowerCase = fieldNameNode.innerText.toLowerCase();
            const fieldIsHidden = !fieldNameLowerCase.includes(fieldFilterQueryLowerCase);

            field.classList.toggle('.ibexa-field-types-available__field--hidden', fieldIsHidden);
        });
    };

    doc.querySelector('.ibexa-field-types-available__search').addEventListener('keyup', searchField, false);
})(window, window.document);
