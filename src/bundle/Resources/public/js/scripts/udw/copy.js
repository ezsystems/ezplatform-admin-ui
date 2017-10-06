(function () {
    const btns = document.querySelectorAll('.btn--udw-copy');
    const form = document.querySelector('form[name="location_copy"]');
    const input = form.querySelector('#location_copy_data_new_parent_location');
    const udwContainer = document.getElementById('react-udw'); 
    const closeUDW = () => udwContainer.innerHTML = '';
    const contentDiscoverHandler = (items) => {
        closeUDW();

        input.value = items[0].id;
        form.submit();
    };
    const cancelDiscoverHandler = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        ReactDOM.render(React.createElement(UniversalDiscovery.default, {
            contentDiscoverHandler: contentDiscoverHandler,
            cancelDiscoverHandler: cancelDiscoverHandler,
            confirmLabel: 'Copy to location',
            title: 'Select location',
            multiple: false,
            startingLocationId: parseInt(event.currentTarget.dataset.rootLocation, 10)
        }), udwContainer);
    };

    btns.forEach(btn => btn.addEventListener('click', openUDW, false));
})();
