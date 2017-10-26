(function () {
    const btns = document.querySelectorAll('.btn--open-udw');
    const udwContainer = document.getElementById('react-udw');
    const token = document.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => udwContainer.innerHTML = '';
    const onConfirm = (form, content) => {
        console.log(form, form.getAttribute('name'));
        const field = form.querySelector('#' + form.getAttribute('name') + '_data_locations_location');

        field.value = content.map(item => item.id).join();

        closeUDW();
        form.submit();
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const form = event.target.closest('form');

        ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, {
            onConfirm: onConfirm.bind(this, form),
            onCancel,
            restInfo: {token, siteaccess}
        }), udwContainer);
    };

    btns.forEach(btn => btn.addEventListener('click', openUDW, false));
})();
