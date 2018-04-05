(function (global, doc) {
    const btns = doc.querySelectorAll('.ez-btn--cotf-create');
    const udwContainer = doc.getElementById('react-udw');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (items) => {
        closeUDW();

        window.location.href = window.Routing.generate('_ezpublishLocation', { locationId: items[0].id });
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, {
            onConfirm,
            onCancel,
            title: 'Create your content',
            activeTab: 'create',
            onlyContentOnTheFly: true,
            multiple: false,
            startingLocationId: global.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
            restInfo: {token, siteaccess}
        }), udwContainer);
    };

    btns.forEach(btn => btn.addEventListener('click', openUDW, false));
})(window, document);
