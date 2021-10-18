(function(global, doc) {
    const revealButton = doc.querySelector('.ibexa-btn--reveal');
    const form = doc.querySelector('form[name="content_visibility_update"]');
    const visiblity = doc.querySelector('#content_visibility_update_visible');

    if (!revealButton) {
        return;
    }

    revealButton.addEventListener(
        'click',
        () => {
            visiblity.value = 1;
            form.submit();
        },
        false
    );
})(window, window.document);
