(function (global, doc) {
    const updateVisibility = event => {
        event.target.closest('.ez-checkbox-icon').classList.toggle('is-checked');

        doc.querySelector('#location_update_visibility_data_location').value = event.target.value;
        doc.querySelector('#location_update_visibility_data_hidden').checked = !event.target.checked;

        doc.querySelector('form[name="location_update_visibility_data"]').submit();
    }

    [...doc.querySelectorAll('.ez-checkbox-icon__checkbox')].forEach(checkbox => checkbox.addEventListener('change', updateVisibility, false));
})(window, document);
