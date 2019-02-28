(function(global, doc) {
    const revealButton = doc.querySelector('.ez-btn--reveal');
    const form = doc.querySelector('form[name="content"]');

    if (!revealButton) {
        return;
    }

    revealButton.addEventListener(
        'click',
        () => {
            form.submit();
        },
        false
    );
})(window, document);
