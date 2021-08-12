(function(global, doc, eZ) {
    const dropdowns = doc.querySelectorAll('.ibexa-dropdown');

    dropdowns.forEach((dropdownContainer) => {
        const dropdown = new eZ.core.Dropdown({
            container: dropdownContainer,
        });

        dropdown.init();
    });
})(window, window.document, window.eZ);
