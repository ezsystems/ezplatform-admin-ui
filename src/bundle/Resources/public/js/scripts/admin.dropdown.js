(function(global, doc, eZ) {
    const dropdowns = doc.querySelectorAll('.ibexa-dropdown:not(.ibexa-dropdown--custom-init)');

    dropdowns.forEach((dropdownContainer) => {
        const dropdown = new eZ.core.Dropdown({
            container: dropdownContainer,
        });

        dropdown.init();
    });
})(window, window.document, window.eZ);
