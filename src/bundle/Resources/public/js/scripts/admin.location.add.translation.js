(function(global, doc) {
    const SELECTOR_MODAL = '.ez-modal';

    doc.querySelectorAll('.ez-translation .ez-custom-dropdown__wrapper').forEach((container) => {
        const dropdown = new eZ.core.CustomDropdown({
            container,
            itemsContainer: container.querySelector('.ez-custom-dropdown__items'),
            sourceInput: doc.querySelector(container.dataset.sourceSelector),
        });

        dropdown.init();
    });

    doc.querySelectorAll('.ez-translation__language-wrapper--language').forEach((select) => {
        select.addEventListener(
            'valueChanged',
            (event) => {
                const modal = event.target.closest(SELECTOR_MODAL);
                const buttonCreate = modal.querySelector('.ez-btn--create-translation');
                const method = event.target.value ? 'removeAttribute' : 'setAttribute';

                buttonCreate[method]('disabled', true);
            },
            false
        );
    });
})(window, window.document);
