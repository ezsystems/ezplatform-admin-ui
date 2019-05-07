(function(global, doc) {
    const autosubmit = (event) => {
        const form = event.target.closest('form');

        form.submit();
    };
    const items = doc.querySelectorAll('.ez-form-autosubmit');

    items.forEach((item) => item.addEventListener('change', autosubmit, false));
})(window, window.document);
