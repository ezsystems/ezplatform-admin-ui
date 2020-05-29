(function (global, doc) {
    const checkboxes = doc.querySelectorAll('.ez-field-edit--ezboolean input');
    const toggleCheckbox = (event) => {
        const checkbox = event.target;

        checkbox.closest('.ez-data-source__label').classList.toggle('is-checked', checkbox.checked);
    };

    checkboxes.forEach((checkbox) => checkbox.addEventListener('change', toggleCheckbox, false));
})(window, window.document);
