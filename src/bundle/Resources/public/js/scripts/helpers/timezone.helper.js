(function(global, doc, eZ, moment) {
    const userPreferedTimezone = eZ.adminUiConfig.timezone;
    const userPreferedFullDateFormat = eZ.adminUiConfig.dateFormat.full;
    const userPreferedShortDateFormat = eZ.adminUiConfig.dateFormat.short;

    const convertDateToTimezone = (date, timezone = userPreferedTimezone) => {
        return moment(date).tz(timezone);
    };
    const formatDate = (date, format = userPreferedFullDateFormat) => {
        return moment(date).formatICU(format);
    };
    const formatDateWithTimezone = (date, timezone = userPreferedTimezone, format = userPreferedFullDateFormat) => {
        return formatDate(convertDateToTimezone(date, timezone), format);
    };
    const formatShortDate = (date, format = userPreferedShortDateFormat) => {
        return formatDate(date, format);
    };
    const formatShortDateWithTimezone = (date, timezone = userPreferedTimezone, format = userPreferedShortDateFormat) => {
        return formatDateWithTimezone(date, timezone, format);
    };

    eZ.addConfig('helpers.timezone', {
        convertDateToTimezone,
        formatDate,
        formatShortDate,
        formatDateWithTimezone,
        formatShortDateWithTimezone,
    });
})(window, document, window.eZ, window.moment);
