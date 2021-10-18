(function(global, doc, eZ, React, ReactDOM, Translator) {
    const btns = doc.querySelectorAll('.ibexa-btn--udw-add');
    const submitButton = doc.querySelector('#content_location_add_add');
    const form = doc.querySelector('form[name="content_location_add"]');
    const input = form.querySelector('#content_location_add_new_locations');
    const udwContainer = doc.getElementById('react-udw');
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (items) => {
        closeUDW();

        input.value = items[0].id;
        submitButton.click();
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();
        event.stopPropagation();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);
        const title = Translator.trans(/*@Desc("Select Location")*/ 'add_location.title', {}, 'universal_discovery_widget');

        ReactDOM.render(
            React.createElement(eZ.modules.UniversalDiscovery, {
                onConfirm,
                onCancel,
                containersOnly: true,
                title,
                multiple: false,
                ...config,
            }),
            udwContainer
        );
    };

    btns.forEach((btn) => btn.addEventListener('click', openUDW, false));
})(window, window.document, window.eZ, window.React, window.ReactDOM, window.Translator);
