(function (global, doc) {

    const autosubmit = event => {
        let form  = event.target.closest('form');
        form.submit();
    };

    const items = doc.querySelectorAll('.ez_form_autosubmit');
    items.forEach(item => item.addEventListener('change', autosubmit, false));

})(window, document);
