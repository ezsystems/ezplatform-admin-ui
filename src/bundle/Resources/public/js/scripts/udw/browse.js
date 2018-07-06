(function() {
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
        const confirmLabel = global.Translator.trans(/*@Desc("View content")*/ 'confirm', {}, 'admin_ui_frontend_udw_browse');
        const title = global.Translator.trans(/*@Desc("Browse content")*/ 'title', {}, 'admin_ui_frontend_udw_browse');

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
})();
