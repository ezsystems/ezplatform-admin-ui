(function () {
    const btns = document.querySelectorAll('.btn--open-udw');
    const udwContainer = document.getElementById('react-udw'); 
    const closeUDW = () => udwContainer.innerHTML = '';
    const contentDiscoverHandler = (form, content) => {
        console.log(form, form.getAttribute('name'));
        const field = form.querySelector('#' + form.getAttribute('name') + '_data_locations_location');

        field.value = content.map(item => item.id).join();

        closeUDW();
        form.submit();
    };
    const cancelDiscoverHandler = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const form = event.target.closest('form');

        ReactDOM.render(React.createElement(UniversalDiscovery.default, {
            contentDiscoverHandler: contentDiscoverHandler.bind(this, form),
            cancelDiscoverHandler: cancelDiscoverHandler,
        }), udwContainer);
    };

    btns.forEach(btn => btn.addEventListener('click', openUDW, false));
})();
