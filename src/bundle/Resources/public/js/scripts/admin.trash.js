(function(global, doc) {
    const form = doc.querySelector('form[name="location_trash"]');
    const submitButton = form.querySelector('button[type="submit"]');
    const allOptions = form.querySelectorAll('.ez-modal__trash-option');
    const confirmCheckbox = form.querySelector('input[name="location_trash[confirm][]"]');
    const enableButton = (button) => {
        button.disabled = false;
        button.classList.remove('disabled');
    };
    const disableButton = (button) => {
        button.disabled = true;
        button.classList.add('disabled');
    };
    const refreshTrashModal = (event) => {
        const { numberOfSubitems } = event.detail;
        const sendToTrashModal = document.querySelector('.ez-modal--trash-location');
        const modalBody = sendToTrashModal.querySelector('.modal-body');
        const modalSendToTrashButton = sendToTrashModal.querySelector('.modal-footer .ibexa-btn--confirm-send-to-trash');
        const { contentName } = sendToTrashModal.dataset;

        if (numberOfSubitems) {
            const message = Translator.trans(
                /*@Desc("Sending '%content_name%' and its %children_count% Content item(s) to Trash will also send the sub-items of this Location to Trash.")*/ 'trash_container.modal.message_main',
                {
                    content_name: contentName,
                    children_count: numberOfSubitems,
                },
                'content'
            );

            modalBody.querySelector('.ez-modal__option-description').innerHTML = message;
        } else {
            const message = Translator.trans(
                /*@Desc("Are you sure you want to send this Content item to Trash?")*/ 'trash.modal.message',
                {},
                'content'
            );

            modalBody.innerHTML = message;
            modalSendToTrashButton.removeAttribute('disabled');
            modalSendToTrashButton.classList.remove('disabled');
        }
    };

    doc.body.addEventListener('ez-trash-modal-refresh', refreshTrashModal, false);

    if (!confirmCheckbox) {
        enableButton(submitButton);

        return;
    }

    const toggleSubmitButton = () => {
        const areAllOptionsChecked = [...allOptions].every((option) => {
            const inputs = [...option.querySelectorAll('input')];
            const isInputChecked = (input) => input.checked;

            return inputs.length === 0 || inputs.some(isInputChecked);
        });

        areAllOptionsChecked && confirmCheckbox.checked ? enableButton(submitButton) : disableButton(submitButton);
    };

    form.addEventListener('change', toggleSubmitButton, false);
})(window, document);
