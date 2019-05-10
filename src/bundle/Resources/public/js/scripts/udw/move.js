(function(global, doc, eZ, React, ReactDOM, Translator) {
    const btns = doc.querySelectorAll('.btn--udw-move');
    const form = doc.querySelector('form[name="location_move"]');
    const input = form.querySelector('#location_move_new_parent_location');
    const udwContainer = doc.getElementById('react-udw');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (items) => {
        closeUDW();

        input.value = items[0].id;
        form.submit();
    };
    const canSelectContent = ({ item }, callback) => callback(item.ContentInfo.Content.ContentTypeInfo.isContainer);
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);
        const title = Translator.trans(/*@Desc("Select destination")*/ 'move.title', {}, 'universal_discovery_widget');

        ReactDOM.render(
            React.createElement(eZ.modules.UniversalDiscovery, {
                onConfirm,
                onCancel,
                canSelectContent,
                title,
                multiple: false,
                startingLocationId: parseInt(event.currentTarget.dataset.rootLocation, 10),
                restInfo: { token, siteaccess },
                allowContainersOnly: true,
                ...config,
            }),
            udwContainer
        );
    };

    btns.forEach((btn) => btn.addEventListener('click', openUDW, false));
})(window, window.document, window.eZ, window.React, window.ReactDOM, window.Translator);
