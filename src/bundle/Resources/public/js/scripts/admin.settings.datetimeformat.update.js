(function(global, doc, moment) {
    const SELECTOR_DATE_FORMAT = '#user_setting_update_value_date_format';
    const SELECTOR_TIME_FORMAT = '#user_setting_update_value_time_format';
    const SELECTOR_VALUE_PREVIEW = '.ez-datetime-format-preview-value';
    const valuePreview = doc.querySelector(SELECTOR_VALUE_PREVIEW);
    const dateFormatSelect = doc.querySelector(SELECTOR_DATE_FORMAT);
    const timeFormatSelect = doc.querySelector(SELECTOR_TIME_FORMAT);
    const updateDateTimeFormatPreview = () => {
        valuePreview.innerHTML = moment().formatICU(`${dateFormatSelect.value} ${timeFormatSelect.value}`);
    };

    dateFormatSelect.addEventListener('change', updateDateTimeFormatPreview);
    timeFormatSelect.addEventListener('change', updateDateTimeFormatPreview);

    updateDateTimeFormatPreview();
})(window, window.document, window.moment);
