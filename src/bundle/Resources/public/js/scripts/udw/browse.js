(function(global, doc, eZ, React, ReactDOM, Translator, Routing) {
    const btns = doc.querySelectorAll('.btn--udw-browse');
    const udwContainer = doc.getElementById('react-udw');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (items) => {
        closeUDW();

        global.location.href = Routing.generate('_ezpublishLocation', { locationId: items[0].id });
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);
        const title = Translator.trans(/*@Desc("Browse content")*/ 'browse.title', {}, 'universal_discovery_widget');

        ReactDOM.render(
            React.createElement(eZ.modules.UniversalDiscovery, {
                onConfirm,
                onCancel,
                title,
                multiple: false,
                startingLocationId: parseInt(event.currentTarget.dataset.startingLocationId, 10),
                restInfo: { token, siteaccess },
                ...config,
            }),
            udwContainer
        );
    };

    btns.forEach((btn) => btn.addEventListener('click', openUDW, false));

    // Hardcoded

    const newUdwBtn = doc.querySelector('.btn--new-udw-browse');
    const openNewUDW = (event) => {
        event.preventDefault();

        const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
        const onConfirm = (items) => {
            closeUDW();

            global.location.href = Routing.generate('_ezpublishLocation', { locationId: items[0].id });
        };

        const newConfig = {
            onConfirm,
            onCancel: closeUDW,
            tabs: window.eZ.adminUiConfig.universalDiscoveryWidget.tabs,
            title: 'Browsing content',
        };

        ReactDOM.render(React.createElement(eZ.modules.UDW, newConfig), udwContainer);
    };

    newUdwBtn.addEventListener('click', openNewUDW, false);
})(window, window.document, window.eZ, window.React, window.ReactDOM, window.Translator, window.Routing);
