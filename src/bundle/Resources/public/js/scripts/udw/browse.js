(function(Translator) {
    const btns = document.querySelectorAll('.btn--udw-browse');
    const udwContainer = document.getElementById('react-udw');
    const token = document.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (items) => {
        closeUDW();

        window.location.href = window.Routing.generate('_ezpublishLocation', { locationId: items[0].id });
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);
        const confirmLabel = Translator.trans(/*@Desc("View content")*/ 'browse.confirm.label', {}, 'universal_discovery_widget');
        const title = Translator.trans(/*@Desc("Browse content")*/ 'browse.title', {}, 'universal_discovery_widget');

        ReactDOM.render(
            React.createElement(
                eZ.modules.UniversalDiscovery,
                Object.assign(
                    {
                        onConfirm,
                        onCancel,
                        confirmLabel,
                        title,
                        multiple: false,
                        startingLocationId: parseInt(event.currentTarget.dataset.startingLocationId, 10),
                        restInfo: { token, siteaccess },
                    },
                    config
                )
            ),
            udwContainer
        );
    };

    btns.forEach((btn) => btn.addEventListener('click', openUDW, false));
})(window.Translator);
