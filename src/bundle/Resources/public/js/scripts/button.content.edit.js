(function (global, doc, $) {
    const FORM_EDIT = 'form.ez-edit-content-form';
    const editVersion = (event) => {
        const versionEditForm = doc.querySelector(FORM_EDIT);
        const versionEditFormName = versionEditForm.name;
        const contentId = event.currentTarget.dataset.contentId;
        const versionNo = event.currentTarget.dataset.versionNo;
        const languageCode = event.currentTarget.dataset.languageCode;
        const contentInfoInput = versionEditForm.querySelector('input[name="' + versionEditFormName + '[content_info]"]');
        const versionInfoContentInfoInput = versionEditForm.querySelector('input[name="' + versionEditFormName+ '[version_info][content_info]"]');
        const versionInfoVersionNoInput = versionEditForm.querySelector('input[name="' + versionEditFormName + '[version_info][version_no]"]');
        const languageInput = versionEditForm.querySelector('#'+ versionEditFormName +'_language_' + languageCode);
        const checkVersionDraftLink = global.Routing.generate('ezplatform.version_draft.has_no_conflict', { contentId });
        const submitVersionEditForm = () => {
            contentInfoInput.value = contentId;
            versionInfoContentInfoInput.value = contentId;
            versionInfoVersionNoInput.value = versionNo;
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
            [...wrapper.querySelectorAll('.ez-btn--prevented')].forEach(btn => btn.addEventListener('click', event => event.preventDefault(), false));
            $('#version-draft-conflict-modal').modal('show');
        };

        event.preventDefault();

        fetch(checkVersionDraftLink, {
            credentials: 'same-origin'
        }).then(function (response) {
            // Status 409 means that a draft conflict has occurred and the modal must be displayed.
            // Otherwise we can go to Content Item edit page.
            if (response.status === 409) {
                response.text().then(showModal);
            } else if (response.status === 200) {
                submitVersionEditForm();
            }
        });
    };

    [...doc.querySelectorAll('.ez-btn--content-edit')].forEach(button => button.addEventListener('click', editVersion, false));
})(window, document, window.jQuery);
