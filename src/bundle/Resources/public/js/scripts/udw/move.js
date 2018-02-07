(function () {
    const btns = document.querySelectorAll('.btn--udw-move');
    const form = document.querySelector('form[name="location_move"]');
    const input = form.querySelector('#location_move_new_parent_location');
    const udwContainer = document.getElementById('react-udw');
    const token = document.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (items) => {
        closeUDW();

        input.value = items[0].id;
        form.submit();
    };
    const canSelectContent = ({item}, callback) => callback(item.ContentInfo.Content.ContentTypeInfo.isContainer);
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        window.ReactDOM.render(window.React.createElement(window.eZ.modules.UniversalDiscovery, {
            onConfirm,
            onCancel,
            canSelectContent,
            confirmLabel: 'Move to location',
            title: 'Select destination',
            multiple: false,
            startingLocationId: parseInt(event.currentTarget.dataset.rootLocation, 10),
            restInfo: {token, siteaccess}
        }), udwContainer);
    };

    btns.forEach(btn => btn.addEventListener('click', openUDW, false));
})();
