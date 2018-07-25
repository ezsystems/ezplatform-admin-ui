(function(global, doc, eZ, React, ReactDOM, Translator) {
    const btns = doc.querySelectorAll('.btn--udw-add');
    const submitButton = doc.querySelector('#content_location_add_add');
    const form = doc.querySelector('form[name="content_location_add"]');
    const input = form.querySelector('#content_location_add_new_locations');
    const udwContainer = doc.getElementById('react-udw');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (items) => {
        closeUDW();

        input.value = items[0].id;
        submitButton.click();
    };
    const canSelectContent = ({ item }, callback) => callback(item.ContentInfo.Content.ContentTypeInfo.isContainer);
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();
        event.stopPropagation();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);
        const title = Translator.trans(/*@Desc("Select location")*/ 'add_location.title', {}, 'universal_discovery_widget');

        ReactDOM.render(
            React.createElement(
                eZ.modules.UniversalDiscovery,
                Object.assign(
                    {
                        onConfirm,
                        onCancel,
                        canSelectContent,
                        title,
                        multiple: false,
                        startingLocationId: eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
                        restInfo: { token, siteaccess },
                    },
                    config
                )
            ),
            udwContainer
        );
    };

    btns.forEach((btn) => btn.addEventListener('click', openUDW, false));
})(window, document, window.eZ, window.React, window.ReactDOM, window.Translator);
