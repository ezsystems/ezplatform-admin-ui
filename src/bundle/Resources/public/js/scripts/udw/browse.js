(function () {
    const btns = document.querySelectorAll('.btn--udw-browse');
    const udwContainer = document.getElementById('react-udw'); 
    const closeUDW = () => udwContainer.innerHTML = '';
    const contentDiscoverHandler = (items) => {
        closeUDW();

        // @todo hardcoded link has to go...
        window.location.href = `/admin/content/location/${items[0].id}`;
    };
    const cancelDiscoverHandler = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        ReactDOM.render(React.createElement(UniversalDiscovery.default, {
            contentDiscoverHandler: contentDiscoverHandler,
            cancelDiscoverHandler: cancelDiscoverHandler,
            confirmLabel: 'View content',
            title: 'Browse content',
            multiple: false,
            startingLocationId: parseInt(event.currentTarget.dataset.startingLocationId, 10)
        }), udwContainer);
    };

    btns.forEach(btn => btn.addEventListener('click', openUDW, false));
})();
