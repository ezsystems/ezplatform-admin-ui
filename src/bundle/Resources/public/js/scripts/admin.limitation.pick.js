(function(global, doc, eZ, React, ReactDOM, Translator) {
    const udwContainer = doc.getElementById('react-udw');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const selectLocationsConfirm = (target, data) => {
        const selectedItems = data.reduce((total, item) => total + `<li>${item.ContentInfo.Content.Name}</li>`, '');

        doc.querySelector(target.dataset.locationInputSelector).value = data.map((item) => item.id).join();
        doc.querySelector(target.dataset.selectedLocationListSelector).innerHTML = selectedItems;

        closeUDW();
    };
    const openUDW = (event) => {
        event.preventDefault();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);
        const title = Translator.trans(/*@Desc("Choose locations")*/ 'subtree_limitation.title', {}, 'universal_discovery_widget');

        ReactDOM.render(
            React.createElement(
                eZ.modules.UniversalDiscovery,
                Object.assign(
                    {
                        onConfirm: selectLocationsConfirm.bind(this, event.target),
                        onCancel: closeUDW,
                        title,
                        startingLocationId: eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
                        multiple: true,
                        restInfo: { token, siteaccess },
                    },
                    config
                )
            ),
            udwContainer
        );
    };

    [...doc.querySelectorAll('.ez-pick-location-limitation-button')].forEach((btn) => btn.addEventListener('click', openUDW, false));
})(window, document, window.eZ, window.React, window.ReactDOM, window.Translator);
