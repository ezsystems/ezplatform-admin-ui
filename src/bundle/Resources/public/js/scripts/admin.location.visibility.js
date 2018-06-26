(function(global, doc) {
    const SELECTOR_VISIBILITY_CHECKBOXES = '#ez-tab-location-view-locations .ez-checkbox-icon__checkbox';
    const fetchInit = {
        credentials: 'same-origin',
    };
    const visibilityCheckboxes = [...doc.querySelectorAll(SELECTOR_VISIBILITY_CHECKBOXES)];

    const onVisibilityUpdated = (event) => {
        const { target } = event;
        const { checked: isHidden } = target;

        target.closest('.ez-checkbox-icon').classList.toggle('is-checked', isHidden);
    };
    const handleUpdateError = global.eZ.services.notification.showErrorNotification;
    const handleUpdateResponse = (event) => (response) => {
        if (response.status === 200) {
            onVisibilityUpdated(event);
            return;
        }

        throw new Error(response.statusText);
    };
    const updateVisibility = (event) => {
        const { value: locationId, checked: isVisible } = event.target;
        const isHiddenNumberValue = !isVisible ? 1 : 0;
        const changeVisibilityLink = window.Routing.generate('ezplatform.location.hide', { locationId, hidden: isHiddenNumberValue });

        fetch(changeVisibilityLink, fetchInit)
            .then(handleUpdateResponse(event))
            .catch(handleUpdateError);
    };

    for (const checkbox of visibilityCheckboxes) {
        checkbox.addEventListener('change', updateVisibility, false);
    }
})(window, document);
