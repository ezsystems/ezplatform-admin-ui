(function (global, doc) {
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

        event.preventDefault();

        contentInfoInput.value = contentId;
        versionInfoContentInfoInput.value = contentId;
        versionInfoVersionNoInput.value = versionNo;
        languageInput.setAttribute('checked', true);

        versionEditForm.submit();
    };

    [...doc.querySelectorAll('.ez-btn--content-edit')].forEach(button => button.addEventListener('click', editVersion, false));
})(window, document);
