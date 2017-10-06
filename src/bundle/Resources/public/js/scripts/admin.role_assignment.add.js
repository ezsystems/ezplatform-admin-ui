(function () {
    const btns = document.querySelectorAll('.btn--open-udw');
    const udwContainer = document.getElementById('react-udw');
    const closeUDW = () => udwContainer.innerHTML = '';
    const contentDiscoverHandler = (form, content) => {
        const field = form.querySelector('#role_assignment_locations_value');
        field.value = content.map(item => item.ContentInfo.Content._id).join();
        closeUDW();
    };
    const cancelDiscoverHandler = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const form = event.target.closest('form[name=role_assignment]');
        ReactDOM.render(React.createElement(UniversalDiscovery.default, {
            contentDiscoverHandler: contentDiscoverHandler.bind(this, form),
            cancelDiscoverHandler: cancelDiscoverHandler,
        }), udwContainer);
    };

    btns.forEach(btn => btn.addEventListener('click', openUDW, false));
})();
