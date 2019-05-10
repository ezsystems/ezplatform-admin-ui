(function(global, doc, eZ, React, ReactDOM) {
    const btns = doc.querySelectorAll('.btn--open-udw');
    const udwContainer = doc.getElementById('react-udw');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (form, content) => {
        const field = form.querySelector(`#${form.getAttribute('name')}_locations_location`);

        field.value = content.map((item) => item.id).join();

        closeUDW();
        form.submit();
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const form = doc.querySelector('form[name="section_content_assign"]');
        const btn = event.target.closest('a');
        const config = JSON.parse(btn.dataset.udwConfig);

        form.action = btn.dataset.formAction;
        doc.querySelector('#section_content_assign_section').value = btn.dataset.sectionId;

        ReactDOM.render(
            React.createElement(eZ.modules.UniversalDiscovery, {
                onConfirm: onConfirm.bind(this, form),
                onCancel,
                startingLocationId: eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
                restInfo: { token, siteaccess },
                config,
            }),
            udwContainer
        );
    };

    btns.forEach((btn) => btn.addEventListener('click', openUDW, false));
})(window, window.document, window.eZ, window.React, window.ReactDOM);
