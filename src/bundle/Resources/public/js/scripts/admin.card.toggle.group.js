(function(global, doc) {
    const togglers = doc.querySelectorAll('.ez-card__body-display-toggler');
    const toggleFieldTypeView = (event) => {
        event.preventDefault();

        event.currentTarget.closest('.ez-card--toggle-group').classList.toggle('ez-card--collapsed');
    };

    togglers.forEach((btn) => btn.addEventListener('click', toggleFieldTypeView, false));
})(window, window.document);
