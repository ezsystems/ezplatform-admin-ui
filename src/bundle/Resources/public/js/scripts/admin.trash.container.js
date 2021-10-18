(function(global, doc, bootstrap) {
    const toggleForm = doc.querySelector('form[name="location_trash_container"]');
    const hasAsset = toggleForm.dataset.hasAsset;
    const hasUniqueAsset = toggleForm.dataset.hasUniqueAsset;

    const openTrashImageAssetModal = (event) => {
        if (!hasAsset && !hasUniqueAsset) {
            return;
        }

        event.preventDefault();

        bootstrap.Modal.getOrCreateInstance(doc.querySelector('#trash-container-modal')).hide();
        bootstrap.Modal.getOrCreateInstance(doc.querySelector('#trash-with-asset-modal')).show();
    };

    toggleForm.addEventListener('submit', openTrashImageAssetModal, false);
})(window, window.document, window.bootstrap);
