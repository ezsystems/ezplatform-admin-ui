(function(global, doc, eZ, moment) {
    const userPreferedTimezone = eZ.adminUiConfig.timezone;
    const userPreferedFullDateFormat = eZ.adminUiConfig.dateFormat.full;
    const userPreferedShortDateFormat = eZ.adminUiConfig.dateFormat.short;

    const convertDateToTimezone = (date, timezone = userPreferedTimezone) => {
        return moment(date).tz(timezone);
    };
    const formatDate = (date, format = userPreferedFullDateFormat) => {
        return moment(date).formatPHP(format);
    };
    const formatShortDate = (date, format = userPreferedShortDateFormat) => {
        return moment(date).formatPHP(format);
    };
    const formatDateWithTimezone = (date, timezone = userPreferedTimezone, format = userPreferedFullDateFormat) => {
        return formatDate(convertDateToTimezone(date, timezone), format);
    };
    const formatShortDateWithTimezone = (date, timezone = userPreferedTimezone, format = userPreferedShortDateFormat) => {
        return formatDate(convertDateToTimezone(date, timezone), format);
    };

    eZ.addConfig('helpers.timezone', {
        convertDateToTimezone,
        formatDate,
        formatShortDate,
        formatDateWithTimezone,
        formatShortDateWithTimezone,
    });
})(window, document, window.eZ, window.moment);
