(function(global, doc, $) {
    const hideButton = doc.querySelector('.ez-btn--hide');
    const modal = doc.querySelector('#hide-content-modal');
    const form = doc.querySelector('form[name="content_visibility_update"]');
    const visiblity = doc.querySelector('#content_visibility_update_visible');

    if (!hideButton) {
        return;
    }

    if (modal) {
        modal.querySelector('.btn-confirm').addEventListener('click', () => {
            visiblity.value = 0;
            form.submit();
        });
    }

    hideButton.addEventListener(
        'click',
        () => {
            if (modal) {
                $(modal).modal('show');
            } else {
                visiblity.value = 0;
                form.submit();
            }
        },
        false
    );
})(window, window.document, window.jQuery);
