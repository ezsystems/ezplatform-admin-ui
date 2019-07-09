(function(global, doc, eZ, React, ReactDOM) {
    const btns = doc.querySelectorAll('.btn--udw-relation-default-location');
    const udwContainer = doc.getElementById('react-udw');
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (btn, items) => {
        closeUDW();

        const locationId = items[0].id;
        const locationName = items[0].ContentInfo.Content.TranslatedName;
        const objectRelationListSettingsWrapper = btn.closest('.ezobjectrelationlist-settings');
        const objectRelationSettingsWrapper = btn.closest('.ezobjectrelation-settings');

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

    btns.forEach((btn) => btn.addEventListener('click', openUDW, false));
})(window, window.document, window.eZ, window.React, window.ReactDOM);
