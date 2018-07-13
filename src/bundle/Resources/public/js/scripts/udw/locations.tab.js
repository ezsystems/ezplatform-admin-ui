(function(Translator) {
    const btns = document.querySelectorAll('.btn--udw-add');
    const submitButton = document.querySelector('#content_location_add_add');
    const form = document.querySelector('form[name="content_location_add"]');
    const input = form.querySelector('#content_location_add_new_locations');
    const udwContainer = document.getElementById('react-udw');
    const token = document.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
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
        const confirmLabel = Translator.trans(/*@Desc("Add location")*/ 'add_location.confirm.label', {}, 'universal_discovery_widget');
        const title = Translator.trans(/*@Desc("Select location")*/ 'add_location.title', {}, 'universal_discovery_widget');

        window.ReactDOM.render(
            window.React.createElement(
                window.eZ.modules.UniversalDiscovery,
                Object.assign(
                    {
                        onConfirm,
                        onCancel,
                        canSelectContent,
                        confirmLabel,
                        title,
                        multiple: false,
                        startingLocationId: window.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
                        restInfo: { token, siteaccess },
                    },
                    config
                )
            ),
            udwContainer
        );
    };

    btns.forEach((btn) => btn.addEventListener('click', openUDW, false));
})(window.Translator);
