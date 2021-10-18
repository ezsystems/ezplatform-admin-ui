(function (global, doc, eZ, React, ReactDOM) {
    const SELECTOR_RESET_STARTING_LOCATION_BTN = '.ibexa-btn--reset-starting-location';
    const resetStartingLocationBtns = doc.querySelectorAll(SELECTOR_RESET_STARTING_LOCATION_BTN);
    const udwBtns = doc.querySelectorAll('.ibexa-btn--udw-relation-default-location');
    const udwContainer = doc.getElementById('react-udw');
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (btn, items) => {
        closeUDW();

        const locationId = items[0].id;
        const locationName = items[0].ContentInfo.Content.TranslatedName;
        const objectRelationListSettingsWrapper = btn.closest('.ezobjectrelationlist-settings');
        const objectRelationSettingsWrapper = btn.closest('.ezobjectrelation-settings');

        toggleResetStartingLocationBtn(btn.parentNode.querySelector(SELECTOR_RESET_STARTING_LOCATION_BTN), true);

        if (objectRelationListSettingsWrapper) {
            objectRelationListSettingsWrapper.querySelector(btn.dataset.relationRootInputSelector).value = locationId;
            objectRelationListSettingsWrapper.querySelector(btn.dataset.relationSelectedRootNameSelector).innerHTML = locationName;
        } else {
            objectRelationSettingsWrapper.querySelector(btn.dataset.relationRootInputSelector).value = locationId;
            objectRelationSettingsWrapper.querySelector(btn.dataset.relationSelectedRootNameSelector).innerHTML = locationName;
        }
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);

        ReactDOM.render(
            React.createElement(eZ.modules.UniversalDiscovery, {
                onConfirm: onConfirm.bind(null, event.currentTarget),
                onCancel,
                title: event.currentTarget.dataset.universaldiscoveryTitle,
                multiple: false,
                ...config,
            }),
            udwContainer
        );
    };
    const toggleResetStartingLocationBtn = (button, isEnabled) => {
        if (isEnabled) {
            button.removeAttribute('disabled');
        } else {
            button.setAttribute('disabled', true);
        }
    };
    const resetStartingLocation = (event) => {
        const button = event.currentTarget;
        const { relationRootInputSelector, relationSelectedRootNameSelector } = button.dataset;

        doc.querySelector(relationRootInputSelector).value = '';
        doc.querySelector(relationSelectedRootNameSelector).innerHTML = '';

        toggleResetStartingLocationBtn(button, false);
    };
    const attachEvents = (btns) => {
        btns.forEach((btn) => btn.addEventListener('click', openUDW, false));
    };

    attachEvents(udwBtns);

    doc.body.addEventListener(
        'ibexa-drop-field-definition',
        (event) => {
            const { nodes } = event.detail;

            nodes.forEach((node) => {
                const btns = node.querySelectorAll('.ibexa-btn--udw-relation-default-location');

                btns.forEach((btn) => btn.addEventListener('click', openUDW, false));
                attachEvents(btns);
            });
        },
        false
    );

    resetStartingLocationBtns.forEach((btn) => btn.addEventListener('click', resetStartingLocation, false));
})(window, window.document, window.eZ, window.React, window.ReactDOM);
