(function(global, doc, eZ, React, ReactDOM, Translator) {
    const btns = doc.querySelectorAll('.ibexa-btn--udw-copy-subtree');
    const form = doc.querySelector('form[name="location_copy_subtree"]');
    const input = form.querySelector('#location_copy_subtree_new_parent_location');
    const udwContainer = doc.querySelector('#react-udw');
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (items) => {
        closeUDW();

        input.value = items[0].id;
        form.submit();
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const title = Translator.trans(/*@Desc("Select Location")*/ 'subtree.title', {}, 'universal_discovery_widget');
        const config = JSON.parse(event.currentTarget.dataset.udwConfig);

        ReactDOM.render(
            React.createElement(eZ.modules.UniversalDiscovery, {
                onConfirm,
                onCancel,
                title,
                multiple: false,
                containersOnly: true,
                ...config,
            }),
            udwContainer
        );
    };

    btns.forEach((btn) => btn.addEventListener('click', openUDW, false));
})(window, window.document, window.eZ, window.React, window.ReactDOM, window.Translator);
