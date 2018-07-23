(function (global, doc, React, ReactDOM) {
    const btns = doc.querySelectorAll('.btn--udw-relation-default-location');
    const udwContainer = doc.getElementById('react-udw');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (btn, items) => {
        closeUDW();

        const locationId = items[0].id;
        const locationName = items[0].ContentInfo.Content.Name;
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

        ReactDOM.render(React.createElement(global.eZ.modules.UniversalDiscovery, Object.assign({
            onConfirm: onConfirm.bind(null, event.currentTarget),
            onCancel,
            title: event.currentTarget.dataset.universaldiscoveryTitle,
            multiple: false,
            startingLocationId: window.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
            restInfo: { token, siteaccess }
        }, config)), udwContainer);
    };

    btns.forEach(btn => btn.addEventListener('click', openUDW, false));
})(window, window.document, window.React, window.ReactDOM);
