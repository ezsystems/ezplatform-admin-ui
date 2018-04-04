(function () {
    const btns = document.querySelectorAll('.btn--open-udw');
    const udwContainer = document.getElementById('react-udw');
    const token = document.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (form, content) => {
        const field = form.querySelector('#trash_item_restore_location_location');

        field.value = content.map(item => item.id).join();

        closeUDW();
        form.submit();
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const form = event.target.closest('form[name="trash_item_restore"]');

        ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, {
            onConfirm: onConfirm.bind(this, form),
            onCancel,
            confirmLabel: 'Restore',
            title: 'Select a location to restore you content item(s)',
            startingLocationId: window.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
            allowContainersOnly: true,
            restInfo: {token, siteaccess},
            multiple: false
        }), udwContainer);
    };

    btns.forEach(btn => btn.addEventListener('click', openUDW, false));

    const checkboxes = [...document.querySelectorAll('form[name="trash_item_restore"] input[type="checkbox"]')];
    const buttonRestore = document.querySelector('#trash_item_restore_restore');
    const buttonRestoreUnderNewParent = document.querySelector('#trash_item_restore_location_select_content');
    const buttonDelete = document.querySelector('#delete-trash-items');

    const enableButtons = (event) => {
        const deleteCheckbox = document.querySelector('form[name="trash_item_delete"] input[type="checkbox"][value="' + event.target.value + '"]');
        const isNonEmptySelection = checkboxes.some(el => el.checked);
        const isMissingParent = checkboxes.some(el => el.checked && parseInt(el.dataset.isParentInTrash, 10) === 1);

        if (deleteCheckbox) {
            deleteCheckbox.checked = event.target.checked;
        }

        if (isNonEmptySelection && !isMissingParent) {
            buttonRestore.removeAttribute('disabled');
        }
        else {
            buttonRestore.setAttribute('disabled', true);
        }

        if (isNonEmptySelection) {
            buttonRestoreUnderNewParent.removeAttribute('disabled');
            buttonDelete.removeAttribute('disabled');
        }
        else {
            buttonRestoreUnderNewParent.setAttribute('disabled', true);
            buttonDelete.setAttribute('disabled', true);
        }
    }

    checkboxes.forEach(checkbox => checkbox.addEventListener('change', enableButtons, false));
})();
