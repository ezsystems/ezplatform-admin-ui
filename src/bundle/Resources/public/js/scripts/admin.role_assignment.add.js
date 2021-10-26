(function(global, doc, eZ, React, ReactDOM) {
    const udwContainer = doc.getElementById('react-udw');
    const limitationsRadio = doc.querySelectorAll('.ez-limitations__radio');
    const selectSubtreeWidget = new eZ.core.TagViewSelect({
        fieldContainer: doc.querySelector('.ez-limitations__item-subtree'),
    });
    const selectUsersWidget = new eZ.core.TagViewSelect({
        fieldContainer: doc.querySelector('.ibexa-assign-users'),
    });
    const selectGroupsWidget = new eZ.core.TagViewSelect({
        fieldContainer: doc.querySelector('.ibexa-assign-groups'),
    });
    const selectSubtreeBtn = doc.querySelector('.ez-limitations__btn-select-subtree');
    const selectUsersBtn = doc.querySelector('#role_assignment_create_users__btn');
    const selectGroupsBtn = doc.querySelector('#role_assignment_create_groups__btn');
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const confirmSubtreeUDW = (data) => {
        const items = data.map((item) => ({
            id: item.id,
            name: eZ.helpers.text.escapeHTML(item.ContentInfo.Content.TranslatedName),
        }));

        selectSubtreeWidget.addItems(items, true);

        closeUDW();
    };
    const openSubtreeUDW = (event) => {
        event.preventDefault();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);
        const selectedLocations = selectSubtreeWidget.inputField.value;
        const selectedLocationsIds = selectedLocations ? selectedLocations.split(',') : [];

        ReactDOM.render(
            React.createElement(eZ.modules.UniversalDiscovery, {
                onConfirm: confirmSubtreeUDW.bind(this),
                onCancel: closeUDW,
                multiple: true,
                selectedLocations: selectedLocationsIds,
                ...config,
            }),
            udwContainer
        );
    };
    const confirmUsersAndGroupsUDW = (widget, selectedItems) => {
        const items = selectedItems.map((item) => ({
            id: item.ContentInfo.Content._id,
            name: item.ContentInfo.Content.Name,
        }));
        const itemsMap = selectedItems.reduce((output, item) => ({ ...output, [item.ContentInfo.Content._id]: item.id }), {});

        widget.addItems(items, true);
        widget.selectBtn.setAttribute('data-items-map', JSON.stringify(itemsMap));

        closeUDW();
    };
    const openUsersAndGroupsUDW = (widget, event) => {
        event.preventDefault();

        const selectBtn = event.currentTarget;
        const config = JSON.parse(selectBtn.dataset.udwConfig);
        const itemsMap = JSON.parse(selectBtn.dataset.itemsMap);
        const selectedContent = widget.inputField.value;
        const selectedContentIds = selectedContent ? selectedContent.split(',') : [];
        const selectedLocationsIds = selectedContentIds.map((contentId) => itemsMap[contentId]);

        ReactDOM.render(
            React.createElement(eZ.modules.UniversalDiscovery, {
                onConfirm: confirmUsersAndGroupsUDW.bind(this, widget),
                onCancel: () => ReactDOM.unmountComponentAtNode(udwContainer),
                title: selectBtn.dataset.universaldiscoveryTitle,
                multiple: true,
                selectedLocations: selectedLocationsIds,
                ...config,
            }),
            udwContainer
        );
    };
    const toggleDisabledState = () => {
        limitationsRadio.forEach((radio) => {
            const disableNode = doc.querySelector(radio.dataset.disableSelector);
            const methodName = radio.checked ? 'removeAttribute' : 'setAttribute';

            if (disableNode) {
                disableNode[methodName]('disabled', 'disabled');
            }
        });
    };

    selectSubtreeBtn.addEventListener('click', openSubtreeUDW, false);
    selectUsersBtn.addEventListener('click', openUsersAndGroupsUDW.bind(null, selectUsersWidget), false);
    selectGroupsBtn.addEventListener('click', openUsersAndGroupsUDW.bind(null, selectGroupsWidget), false);
    limitationsRadio.forEach((radio) => radio.addEventListener('change', toggleDisabledState, false));
})(window, window.document, window.eZ, window.React, window.ReactDOM);
