(function () {
    const btns = document.querySelectorAll('.btn--udw-swap');
    const form = document.querySelector('form[name="location_swap"]');
    const input = form.querySelector('#location_swap_new_location');
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

        ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, Object.assign({
            onConfirm,
            onCancel,
            confirmLabel: 'Swap location',
            title: 'Select location to be swapped with',
            multiple: false,
            startingLocationId: window.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
            restInfo: {token, siteaccess}
        }, config)), udwContainer);
    };

    btns.forEach(btn => btn.addEventListener('click', openUDW, false));
})();
