(function(global, doc, $, eZ) {
    const FORM_EDIT = 'form.ez-edit-content-form';
    const showErrorNotification = eZ.helpers.notification.showErrorNotification;
    const editVersion = (event) => {
        const versionEditForm = doc.querySelector(FORM_EDIT);
        const versionEditFormName = versionEditForm.name;
        const { contentId, versionNo, languageCode } = event.currentTarget.dataset;
        const contentInfoInput = versionEditForm.querySelector(`input[name="${versionEditFormName}[content_info]"]`);
        const versionInfoContentInfoInput = versionEditForm.querySelector(
            `input[name="${versionEditFormName}[version_info][content_info]"]`
        );
        const versionInfoVersionNoInput = versionEditForm.querySelector(`input[name="${versionEditFormName}[version_info][version_no]"]`);
        const languageInput = versionEditForm.querySelector(`#${versionEditFormName}_language_${languageCode}`);
        const checkVersionDraftLink = global.Routing.generate('ezplatform.version_draft.has_no_conflict', { contentId, languageCode });
        const checkEditPermissionLink = global.Routing.generate('ezplatform.content.check_edit_permission', { contentId, languageCode });
        const errorMessage = Translator.trans(
            /*@Desc("You don't have permission to edit the content")*/ 'content.edit.permission.error',
            {},
            'content'
        );
        const submitVersionEditForm = () => {
            contentInfoInput.value = contentId;
            versionInfoContentInfoInput.value = contentId;
            versionInfoVersionNoInput.value = versionNo !== undefined ? versionNo : null;
            languageInput.checked = true;
            versionEditForm.submit();
        };
        const addDraft = () => {
            submitVersionEditForm();
            $('#version-draft-conflict-modal').modal('hide');
        };
        const showModal = (modalHtml) => {
            const wrapper = doc.querySelector('.ez-modal-wrapper');

            wrapper.innerHTML = modalHtml;
            const addDraftButton = wrapper.querySelector('.ez-btn--add-draft');
            if (addDraftButton) {
                addDraftButton.addEventListener('click', addDraft, false);
            }
            [...wrapper.querySelectorAll('.ez-btn--prevented')].forEach((btn) =>
                btn.addEventListener('click', (event) => event.preventDefault(), false)
            );
            $('#version-draft-conflict-modal').modal('show');
        };
        const handleCanEditCheck = (response) => {
            if (response.canEdit) {
                return fetch(checkVersionDraftLink, { mode: 'same-origin', credentials: 'same-origin' });
            }

            throw new Error(errorMessage);
        };
        const handleDraftConflict = (response) => {
            // Status 409 means that a draft conflict has occurred and the modal must be displayed.
            // Otherwise we can go to Content Item edit page.
            if (response.status === 409) {
                response.text().then(showModal);
            } else if (response.status === 403) {
                response.text().then(showErrorNotification);
            } else if (response.status === 200) {
                submitVersionEditForm();
            }
        };

        event.preventDefault();

        fetch(checkEditPermissionLink, { mode: 'same-origin', credentials: 'same-origin' })
            .then(eZ.helpers.request.getJsonFromResponse)
            .then(handleCanEditCheck)
            .then(handleDraftConflict)
            .catch(showErrorNotification);
    };

    [...doc.querySelectorAll('.ez-btn--content-edit')].forEach((button) => button.addEventListener('click', editVersion, false));
})(window, document, window.jQuery, window.eZ);
