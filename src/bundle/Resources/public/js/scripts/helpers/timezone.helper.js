(function(global, doc, eZ, moment) {
    const userPreferredTimezone = eZ.adminUiConfig.timezone;
    const userPreferredFullDateTimeFormat = eZ.adminUiConfig.dateFormat.fullDateTime;
    const userPreferredShortDateTimeFormat = eZ.adminUiConfig.dateFormat.shortDateTime;

    const convertDateToTimezone = (date, timezone = userPreferredTimezone, forceSameTime = false) => {
        return moment(date).tz(timezone, forceSameTime);
    };
    const formatDate = (date, timezone = null, format) => {
        if (timezone) {
            date = convertDateToTimezone(date, timezone);
        }

        return moment(date).formatICU(format);
    };
    const formatFullDateTime = (date, timezone = userPreferredTimezone, format = userPreferredFullDateTimeFormat) => {
        return formatDate(date, timezone, format);
    };
    const formatShortDateTime = (date, timezone = userPreferredTimezone, format = userPreferredShortDateTimeFormat) => {
        return formatDate(date, timezone, format);
    };

    eZ.addConfig('helpers.timezone', {
        convertDateToTimezone,
        formatFullDateTime,
        formatShortDateTime,
    });
})(window, window.document, window.eZ, window.moment);
