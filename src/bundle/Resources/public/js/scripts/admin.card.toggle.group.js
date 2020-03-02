(function(global, doc) {
    const togglers = doc.querySelectorAll('.ez-card__body-display-toggler');
    const toggleFieldTypeView = (event) => {
        event.preventDefault();

        event.currentTarget.closest('.ez-card--toggle-group').classList.toggle('ez-card--collapsed');
    };
    const attachToggleField = (btn) => btn.addEventListener('click', toggleFieldTypeView);

    togglers.forEach((btn) => attachToggleField(btn));

    doc.body.addEventListener('initialize-card-toggle-group', (event) => attachToggleField(event.detail.button));
})(window, window.document);
