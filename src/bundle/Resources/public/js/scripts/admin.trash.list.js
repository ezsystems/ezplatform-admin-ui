(function () {
    const btns = document.querySelectorAll('.btn--open-udw');
    const udwContainer = document.getElementById('react-udw');
    const closeUDW = () => udwContainer.innerHTML = '';
    const contentDiscoverHandler = (form, content) => {
        const field = form.querySelector('#trash_item_restore_data_location_location');

        field.value = content.map(item => item.id).join();

        closeUDW();
        form.submit();
    };
    const cancelDiscoverHandler = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const form = event.target.closest('form[name="trash_item_restore"]');

        ReactDOM.render(React.createElement(UniversalDiscovery.default, {
            contentDiscoverHandler: contentDiscoverHandler.bind(this, form),
            cancelDiscoverHandler: cancelDiscoverHandler,
            confirmLabel: 'Restore',
            title: 'Select a location to restore you content item(s)'
        }), udwContainer);
    };

    btns.forEach(btn => btn.addEventListener('click', openUDW, false));

    const checkboxes = [...document.querySelectorAll('form[name="trash_item_restore"] input[type="checkbox"]')]
    ;
    const buttonRestore = document.querySelector('#trash_item_restore_data_restore');
    const buttonRestoreUnderNewParent = document.querySelector('#trash_item_restore_data_location_select_content');

    const enableButtons = (event) => {
        const isNonEmptySelection = checkboxes.some(el => el.checked);
        const isMissingParent = checkboxes.some(el => el.checked && parseInt(el.dataset.isParentInTrash, 10) === 1);

        if (isNonEmptySelection && !isMissingParent) {
            buttonRestore.removeAttribute('disabled');
        }
        else {
            buttonRestore.setAttribute('disabled', true);
        }

        if (isNonEmptySelection) {
            buttonRestoreUnderNewParent.removeAttribute('disabled');
        }
        else {
            buttonRestoreUnderNewParent.setAttribute('disabled', true);
        }
    }

    checkboxes.forEach(checkbox => checkbox.addEventListener('change', enableButtons, false));
})();
