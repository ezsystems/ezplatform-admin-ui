(function () {
    const listContainers = [...document.querySelectorAll('.ez-sil')];
    const token = document.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;

    listContainers.forEach(container => {
        ReactDOM.render(React.createElement(SubItems.default, {
            startingLocationId: container.dataset.location,
            restInfo: {token, siteaccess}
        }), container);
    });
})();
