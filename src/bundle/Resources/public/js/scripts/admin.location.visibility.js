(function(global, doc) {
    const SELECTOR_VISIBILITY_CHECKBOXES = '#ez-tab-location-view-locations .ez-checkbox-icon__checkbox';
    const SELECTOR_VISIBILITY_FORM = 'form[name="location_update_visibility_data"]';
    const form = doc.querySelector(SELECTOR_VISIBILITY_FORM);
    const visibilityCheckboxes = doc.querySelectorAll(SELECTOR_VISIBILITY_CHECKBOXES);
    const onVisibilityUpdated = ({ target }) => {
        const { checked: isVisible } = target;

        target.closest('.ez-checkbox-icon').classList.toggle('is-checked', isVisible);
    };
    const handleUpdateError = global.eZ.helpers.notification.showErrorNotification;
    const handleUpdateSuccess = (event, { message }) => {
        onVisibilityUpdated(event);
        global.eZ.helpers.notification.showSuccessNotification(message);
    };
    const handleUpdateResponse = (response) => {
        if (response.status !== 200) {
            throw new Error(response.statusText);
        }

        return response.json();
    };
    const updateVisibility = (event) => {
        form.querySelector('#location_update_visibility_data_location').value = event.target.value;
        form.querySelector('#location_update_visibility_data_hidden').checked = !event.target.checked;

        const request = new Request(form.action, {
            method: 'POST',
            body: new FormData(form),
            mode: 'same-origin',
            credentials: 'same-origin',
        });

        fetch(request)
            .then(handleUpdateResponse)
            .then(handleUpdateSuccess.bind(null, event))
            .catch(handleUpdateError);
    };

    visibilityCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', updateVisibility, false);
    });
})(window, document);
