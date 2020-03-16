(function(global, doc, $) {
    const revealButton = doc.querySelector('.ez-btn--reveal');
    const form = doc.querySelector('form[name="content_visibility_update"]');
    const visiblity = doc.querySelector('#content_visibility_update_visible');
    const modal = doc.querySelector('.ez-modal--content-reveal-confirmation');

    if (!revealButton) {
        return;
    }

    if (modal) {
        modal.querySelector('.btn-confirm').addEventListener('click', () => {
            visiblity.value = 1; // to be removed when new form is created
            form.submit();
        });
    }

    revealButton.addEventListener(
        'click',
        () => {
            $(modal).modal('show');
        },
        false
    );
})(window, window.document, window.jQuery);
