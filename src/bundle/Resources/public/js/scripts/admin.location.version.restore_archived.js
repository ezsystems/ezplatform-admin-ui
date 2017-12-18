(function (global, doc) {
    const FORM_ARCHIVED_VERSION_RESTORE = 'form[name="archived_version_restore"]';
    const restoreArchivedVersion = (event) => {
        const versionRestoreForm = doc.querySelector(FORM_ARCHIVED_VERSION_RESTORE);
        const contentId = event.currentTarget.dataset.contentId;
        const versionNo = event.currentTarget.dataset.versionNo;
        const languageCode = event.currentTarget.dataset.languageCode;
        const contentInfoInput = versionRestoreForm.querySelector('input[name="archived_version_restore[content_info]"]');
        const versionInfoContentInfoInput = versionRestoreForm.querySelector('input[name="archived_version_restore[version_info][content_info]"]');
        const versionInfoVersionNoInput = versionRestoreForm.querySelector('input[name="archived_version_restore[version_info][version_no]"]');
        const languageInput = versionRestoreForm.querySelector('#archived_version_restore_language_' + languageCode);

        event.preventDefault();

        contentInfoInput.value = contentId;
        versionInfoContentInfoInput.value = contentId;
        versionInfoVersionNoInput.value = versionNo;
        languageInput.setAttribute('checked', true);

        versionRestoreForm.submit();
    };

    [...doc.querySelectorAll('.ez-btn--restore-archived-version')].forEach(button => button.addEventListener('click', restoreArchivedVersion, false));
})(window, document);
