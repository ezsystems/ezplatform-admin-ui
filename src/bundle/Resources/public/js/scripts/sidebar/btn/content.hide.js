(function(global, doc, $) {
    const hideButton = doc.querySelector('.ez-btn--hide');
    const modal = doc.querySelector('#hide-content-modal');
    const form = doc.querySelector('form[name="content"]');

    if (!hideButton) {
        return;
    }

    if (modal) {
        modal.querySelector('.btn-confirm').addEventListener('click', () => {
            form.submit();
        });
    }

    hideButton.addEventListener(
        'click',
        () => {
            if (modal) {
                $(modal).modal('show');
            } else {
                form.submit();
            }
        },
        false
    );
})(window, document, window.jQuery);
