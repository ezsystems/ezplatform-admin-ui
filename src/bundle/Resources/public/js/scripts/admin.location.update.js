(function(global, doc) {
    const updateMainLocation = (event) => {
        doc.querySelector('#content_main_location_update_location').value = event.target.value;
        doc.querySelector('form[name="content_main_location_update"]').submit();
    };

    doc.querySelectorAll('input[name="updateMainLocation"]').forEach((input) =>
        input.addEventListener('change', updateMainLocation, false)
    );
})(window, window.document);
