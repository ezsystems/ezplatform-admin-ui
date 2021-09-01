(function (global, doc, React, ReactDOM) {
    const SELECTOR_RESET_STARTING_LOCATION_BTN = '.ez-btn--reset-starting-location';
    const resetStartingLocationBtns = doc.querySelectorAll(SELECTOR_RESET_STARTING_LOCATION_BTN);
    const udwBtns = doc.querySelectorAll('.btn--udw-relation-default-location');
    const udwContainer = doc.getElementById('react-udw');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (btn, items) => {
        closeUDW();

        const locationId = items[0].id;
        const locationName = items[0].ContentInfo.Content.TranslatedName;
        const objectRelationListSettingsWrapper = btn.closest('.ezobjectrelationlist-settings');
        const objectRelationSettingsWrapper = btn.closest('.ezobjectrelation-settings');

        toggleResetStartingLocationBtn(
            btn.parentNode.querySelector(SELECTOR_RESET_STARTING_LOCATION_BTN),
            true
        );

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

        ReactDOM.render(React.createElement(global.eZ.modules.UniversalDiscovery, Object.assign({
            onConfirm: onConfirm.bind(null, event.currentTarget),
            onCancel,
            title: event.currentTarget.dataset.universaldiscoveryTitle,
            multiple: false,
            startingLocationId: window.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
            restInfo: { token, siteaccess }
        }, config)), udwContainer);
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

    udwBtns.forEach(btn => btn.addEventListener('click', openUDW, false));
    resetStartingLocationBtns.forEach(btn => btn.addEventListener('click', resetStartingLocation, false));
})(window, window.document, window.React, window.ReactDOM);
