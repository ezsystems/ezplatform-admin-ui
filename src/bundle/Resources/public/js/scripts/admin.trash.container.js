(function(global, doc, $) {
    const toggleForm = doc.querySelector('form[name="location_trash_container"]');
    const hasAsset = toggleForm.dataset.hasAsset;
    const hasUniqueAsset = toggleForm.dataset.hasUniqueAsset;

    const openTrashImageAssetModal = (event) => {
        if (!hasAsset && !hasUniqueAsset) {
            return;
        }

        event.preventDefault();

        $('#trash-container-modal').modal('hide');
        $('#trash-with-asset-modal').modal('show');
    };

    toggleForm.addEventListener('submit', openTrashImageAssetModal, false);
})(window, window.document, window.jQuery);
