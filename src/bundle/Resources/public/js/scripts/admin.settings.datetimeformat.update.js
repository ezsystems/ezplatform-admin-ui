(function(global, doc, moment) {
    const DATE_FORMAT_SELECTOR = '#user_setting_update_value_date_format';
    const TIME_FORMAT_SELECTOR = '#user_setting_update_value_time_format';
    const VALUE_PREVIEW_SELECTOR = '.ez-datetime-format-preview-value';
    const valuePreview = doc.querySelector(VALUE_PREVIEW_SELECTOR);
    const dateFormatSelect = doc.querySelector(DATE_FORMAT_SELECTOR);
    const timeFormatSelect = doc.querySelector(TIME_FORMAT_SELECTOR);
    const updateDateTimeFormatPreview = () => {
        valuePreview.innerHTML = moment().formatICU(
            dateFormatSelect.value + ' ' + timeFormatSelect.value
        );
    };

    dateFormatSelect.addEventListener('change', updateDateTimeFormatPreview);
    timeFormatSelect.addEventListener('change', updateDateTimeFormatPreview);

    updateDateTimeFormatPreview();
}) (window, window.document, window.moment);
