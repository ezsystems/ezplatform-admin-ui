(function(global, doc) {
    const togglers = doc.querySelectorAll('.ibexa-card__body-display-toggler');
    const toggleFieldTypeView = (event) => {
        event.preventDefault();

        event.currentTarget.closest('.ibexa-card--toggle-group').classList.toggle('ibexa-card--collapsed');
    };
    const attachToggleField = (btn) => btn.addEventListener('click', toggleFieldTypeView);

    togglers.forEach((btn) => attachToggleField(btn));

    doc.body.addEventListener('ez-initialize-card-toggle-group', (event) => attachToggleField(event.detail.button));
})(window, window.document);
