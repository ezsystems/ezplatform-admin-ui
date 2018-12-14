(function(global, doc, eZ, React, ReactDOM, Translator) {
    const btns = doc.querySelectorAll('.btn--open-udw');
    const udwContainer = doc.getElementById('react-udw');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (form, content) => {
        const field = form.querySelector('#trash_item_restore_location_location');

        field.value = content.map((item) => item.id).join();

        closeUDW();
        form.submit();
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const form = event.target.closest('form[name="trash_item_restore"]');
        const config = JSON.parse(event.currentTarget.dataset.udwConfig);
        const title = Translator.trans(
            /*@Desc("Select a location to restore you content item(s)")*/ 'restore_under_new_location.title',
            {},
            'universal_discovery_widget'
        );

        ReactDOM.render(
            React.createElement(
                eZ.modules.UniversalDiscovery,
                Object.assign(
                    {
                        onConfirm: onConfirm.bind(this, form),
                        onCancel,
                        title,
                        startingLocationId: eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
                        allowContainersOnly: true,
                        restInfo: { token, siteaccess },
                        multiple: false,
                    },
                    config
                )
            ),
            udwContainer
        );
    };

    btns.forEach((btn) => btn.addEventListener('click', openUDW, false));

    const checkboxes = [...doc.querySelectorAll('form[name="trash_item_restore"] input[type="checkbox"]')];
    const buttonRestore = doc.querySelector('#trash_item_restore_restore');
    const buttonRestoreUnderNewParent = doc.querySelector('#trash_item_restore_location_select_content');
    const buttonDelete = doc.querySelector('#delete-trash-items');

    const enableButtons = () => {
        const isEmptySelection = checkboxes.every((el) => !el.checked);
        const isMissingParent = checkboxes.some((el) => el.checked && parseInt(el.dataset.isParentInTrash, 10) === 1);

        if (buttonRestore) {
            buttonRestore.disabled = isEmptySelection || isMissingParent;
        }

        if (buttonDelete) {
            buttonDelete.disabled = isEmptySelection;
        }

        if (buttonRestoreUnderNewParent) {
            buttonRestoreUnderNewParent.disabled = isEmptySelection;
        }
    };
    const updateTrashForm = (checkboxes) => {
        checkboxes.forEach((checkbox) => {
            const trashFormCheckbox = doc.querySelector(
                'form[name="trash_item_delete"] input[type="checkbox"][value="' + checkbox.value + '"]'
            );

            if (trashFormCheckbox) {
                trashFormCheckbox.checked = checkbox.checked;
            }
        });
    };
    const handleCheckboxChange = (event) => {
        updateTrashForm([event.target]);
        enableButtons();
    };

    updateTrashForm(checkboxes);
    enableButtons();
    checkboxes.forEach((checkbox) => checkbox.addEventListener('change', handleCheckboxChange, false));
})(window, document, window.eZ, window.React, window.ReactDOM, window.Translator);
