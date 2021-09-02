(function(global, doc) {
    const SELECTOR_MODAL = '.ez-modal';

    doc.querySelectorAll('.ez-translation__language-wrapper--language').forEach((select) => {
        select.addEventListener(
            'change',
            (event) => {
                const modal = event.target.closest(SELECTOR_MODAL);
                const buttonCreate = modal.querySelector('.ibexa-btn--create-translation');
                const method = event.target.value ? 'removeAttribute' : 'setAttribute';

                buttonCreate[method]('disabled', true);
            },
            false
        );
    });
})(window, window.document);
