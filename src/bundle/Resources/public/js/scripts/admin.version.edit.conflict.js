(function (global, doc, $, eZ, Translator) {
    const editVersion = (event) => {
        const showErrorNotification = eZ.helpers.notification.showErrorNotification;
        const contentDraftEditUrl = event.currentTarget.dataset.contentDraftEditUrl;
        const versionHasConflictUrl = event.currentTarget.dataset.versionHasConflictUrl;
        const contentId = event.currentTarget.dataset.contentId;
        const languageCode = event.currentTarget.dataset.languageCode;
        const checkEditPermissionLink = global.Routing.generate('ezplatform.content.check_edit_permission', { contentId, languageCode });
        const errorMessage = Translator.trans(
            /*@Desc("You don't have permission to edit the content")*/ 'content.edit.permission.error',
            {},
            'content'
        );
        const handleCanEditCheck = (response) => {
            if (response.canEdit) {
                return fetch(versionHasConflictUrl, { mode: 'same-origin', credentials: 'same-origin' });
            }

            throw new Error(errorMessage);
        };
        const handleVersionDraftConflict = (response) => {
            // Status 409 means that a draft conflict has occurred and the modal must be displayed.
            // Otherwise we can go to Content Item edit page.
            if (response.status === 409) {
                doc.querySelector('#edit-conflicted-draft').href = contentDraftEditUrl;
                $('#version-conflict-modal').modal('show');
            }
            if (response.status === 403) {
                response.text().then(showErrorNotification);
            }
            if (response.status === 200) {
                global.location.href = contentDraftEditUrl;
            }
        };

        event.preventDefault();

        fetch(checkEditPermissionLink, { mode: 'same-origin', credentials: 'same-origin' })
            .then(eZ.helpers.request.getJsonFromResponse)
            .then(handleCanEditCheck)
            .then(handleVersionDraftConflict)
            .catch(showErrorNotification);
    };

    doc.querySelectorAll('.ez-btn--content-draft-edit').forEach((button) => button.addEventListener('click', editVersion, false));
})(window, document, window.jQuery, window.eZ, window.Translator);
