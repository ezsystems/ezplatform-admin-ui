(function () {
    const btns = document.querySelectorAll('.btn--udw-swap');
    const form = document.querySelector('form[name="location_swap"]');
    const input = form.querySelector('#location_swap_data_new_location');
    const udwContainer = document.getElementById('react-udw');
    const token = document.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => udwContainer.innerHTML = '';
    const onConfirm = (items) => {
        closeUDW();

        input.value = items[0].id;
        form.submit();
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, {
            onConfirm,
            onCancel,
            confirmLabel: 'Swap location',
            title: 'Select location to be swapped with',
            multiple: false,
            startingLocationId: parseInt(event.currentTarget.dataset.rootLocation, 10),
            restInfo: {token, siteaccess}
        }), udwContainer);
    };

    btns.forEach(btn => btn.addEventListener('click', openUDW, false));
})();
