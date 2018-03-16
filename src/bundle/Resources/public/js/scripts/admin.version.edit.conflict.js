(function (global, doc, $) {
    const editVersion = (event) => {
        const contentDraftEditUrl = event.currentTarget.dataset.contentDraftEditUrl;
        const versionHasConflictUrl = event.currentTarget.dataset.versionHasConflictUrl;

        event.preventDefault();

        fetch(versionHasConflictUrl, {
            credentials: 'same-origin'
        }).then(function (response) {
            if (response.status === 409) {
                doc.querySelector('#edit-conflicted-draft').href = contentDraftEditUrl;
                $('#version-conflict-modal').modal('show');
            }
            if (response.status === 200) {
                global.location.href = contentDraftEditUrl;
            }
        })
    };

    [...doc.querySelectorAll('.ez-btn--content-draft-edit')].forEach(link => link.addEventListener('click', editVersion, false));
})(window, document, window.jQuery);
