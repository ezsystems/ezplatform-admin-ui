(function(global, doc, eZ, bootstrap, Routing) {
    const editActions = doc.querySelector('.ibexa-extra-actions--edit') || doc.querySelector('.ibexa-extra-actions--edit-user');
    const btns = [...editActions.querySelectorAll('.form-check [type="radio"]')];
    const form = editActions.querySelector('form');
    const contentIdInput = form.querySelector('#content_edit_content_info') || form.querySelector('#user_edit_content_info');
    const contentId = contentIdInput.value;
    const locationInput = form.querySelector('#content_edit_location') || form.querySelector('#user_edit_location');
    const locationId = locationInput.value;
    const resetRadioButtons = () =>
        btns.forEach((btn) => {
            btn.checked = false;
        });
    const addDraft = () => {
        form.submit();
        bootstrap.Modal.getOrCreateInstance(doc.querySelector('#version-draft-conflict-modal')).hide();
    };
    const redirectToUserEdit = (languageCode) => {
        const versionNo = form.querySelector('#user_edit_version_info_version_no').value;

        window.location.href = Routing.generate('ezplatform.user.update', { contentId, versionNo, language: languageCode });
    };
    const onModalHidden = () => {
        resetRadioButtons();

        const event = new CustomEvent('ez-draft-conflict-modal-hidden');

        doc.body.dispatchEvent(event);
    };
    const attachModalListeners = (wrapper) => {
        const addDraftButton = wrapper.querySelector('.ibexa-btn--add-draft');
        const conflictModal = doc.querySelector('#version-draft-conflict-modal');

        if (addDraftButton) {
            addDraftButton.addEventListener('click', addDraft, false);
        }

        wrapper
            .querySelectorAll('.ibexa-btn--prevented')
            .forEach((btn) => btn.addEventListener('click', (event) => event.preventDefault(), false));

        if (conflictModal) {
            bootstrap.Modal.getOrCreateInstance(conflictModal).show();

            conflictModal.addEventListener('hidden.bs.modal', onModalHidden);
            conflictModal.addEventListener('shown.bs.modal', () => eZ.helpers.tooltips.parse());
        }
    };
    const showModal = (modalHtml) => {
        const wrapper = doc.querySelector('.ez-modal-wrapper');

        wrapper.innerHTML = modalHtml;
        attachModalListeners(wrapper);
    };
    const changeHandler = (event) => {
        const checkedBtn = event.currentTarget;
        const languageCode = checkedBtn.value;
        const checkVersionDraftLink = Routing.generate('ezplatform.version_draft.has_no_conflict', { contentId, languageCode, locationId });

        fetch(checkVersionDraftLink, {
            credentials: 'same-origin',
        }).then((response) => {
            if (response.status === 409) {
                response.text().then(showModal);
            } else if (response.status === 200) {
                if (form.querySelector('#user_edit_version_info')) {
                    redirectToUserEdit(languageCode);

                    return;
                }

                form.submit();
            }
        });
    };

    btns.forEach((btn) => btn.addEventListener('change', changeHandler, false));
})(window, window.document, window.eZ, window.bootstrap, window.Routing);
