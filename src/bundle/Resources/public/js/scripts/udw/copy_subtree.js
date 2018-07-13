(function(global, doc, React, ReactDOM, Translator) {
    const btns = [...doc.querySelectorAll('.ez-btn--udw-copy-subtree')];
    const form = doc.querySelector('form[name="location_copy_subtree"]');
    const input = form.querySelector('#location_copy_subtree_new_parent_location');
    const udwContainer = doc.querySelector('#react-udw');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (items) => {
        closeUDW();

        input.value = items[0].id;
        form.submit();
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const title = Translator.trans(/*@Desc("Select location")*/ 'subtree.title', {}, 'universal_discovery_widget');

        ReactDOM.render(
            React.createElement(global.eZ.modules.UniversalDiscovery, {
                onConfirm,
                onCancel,
                title,
                multiple: false,
                startingLocationId: parseInt(event.currentTarget.dataset.rootLocation, 10),
                restInfo: { token, siteaccess },
                allowContainersOnly: true,
            }),
            udwContainer
        );
    };

    btns.forEach((btn) => btn.addEventListener('click', openUDW, false));
})(window, document, window.React, window.ReactDOM, window.Translator);
