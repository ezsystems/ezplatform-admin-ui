(function (global, doc, $) {
    const editActions = doc.querySelector('.ez-extra-actions--edit');
    const btns = [...editActions.querySelectorAll('.form-check [type="radio"]')];
    const form = editActions.querySelector('form');
    const contentId = form.querySelector('#content_edit_content_info').value;
    const checkVersionDraftLink = global.Routing.generate('ezplatform.version_draft.has_no_conflict', { contentId });
    const resetRadioButtons = () => btns.forEach(btn => btn.checked = false);
    const addDraft = () => {
        form.submit();
        $('#version-draft-conflict-modal').modal('hide');
    };

    const changeHandler = () => {
        const showModal = (modalHtml) => {
            const wrapper = doc.querySelector('.ez-modal-wrapper');
            wrapper.innerHTML = modalHtml;
            wrapper.querySelector('.ez-btn--add-draft').addEventListener('click', addDraft, false);
            [...wrapper.querySelectorAll('.ez-btn--prevented')].forEach(btn => btn.addEventListener('click', event => event.preventDefault(), false));
            $('#version-draft-conflict-modal').modal('show').on('hidden.bs.modal', resetRadioButtons);
        };

        fetch(checkVersionDraftLink, {
            credentials: 'same-origin'
        }).then(function (response) {
            if (response.status === 409) {
                response.text().then(showModal);
            } else if (response.status === 200) {
                form.submit();
            }
        });
    };

    btns.forEach(btn => btn.addEventListener('change', changeHandler, false));
})(window, document, window.jQuery);
