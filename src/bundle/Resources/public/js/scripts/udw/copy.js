(function() {
    const btns = document.querySelectorAll('.btn--udw-copy');
    const form = document.querySelector('form[name="location_copy"]');
    const input = form.querySelector('#location_copy_new_parent_location');
    const udwContainer = document.getElementById('react-udw');
    const token = document.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (items) => {
        closeUDW();

        input.value = items[0].id;
        form.submit();
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);
        const confirmLabel = global.Translator.trans(/*@Desc("Copy to location")*/ 'confirm', {}, 'admin_ui_frontend_udw_copy');
        const title = global.Translator.trans(/*@Desc("Select location")*/ 'title', {}, 'admin_ui_frontend_udw_copy');

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
                        startingLocationId: parseInt(event.currentTarget.dataset.rootLocation, 10),
                        restInfo: { token, siteaccess },
                        allowContainersOnly: true,
                    },
                    config
                )
            ),
            udwContainer
        );
    };

    btns.forEach((btn) => btn.addEventListener('click', openUDW, false));
})();
