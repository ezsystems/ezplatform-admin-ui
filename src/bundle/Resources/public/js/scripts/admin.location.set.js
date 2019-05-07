(function(global, doc) {
    const updateMainLocation = (event) => {
        doc.querySelector('#content_location_set_main_location').value = event.target.value;
        doc.querySelector('form[name="content_location_set_main"]').submit();
    };

    doc.querySelectorAll('input[name="setMainLocation"]').forEach((input) => input.addEventListener('change', updateMainLocation, false));
})(window, window.document);
