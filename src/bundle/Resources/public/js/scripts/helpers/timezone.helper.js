(function(global, doc, eZ, moment) {
    const userPreferedTimezone = eZ.adminUiConfig.timezone;
    const userPreferedDateFormat = eZ.adminUiConfig.dateFormat;

    const convertDateToTimezone = (date, timezone = userPreferedTimezone) => {
        return moment(date).tz(timezone);
    };
    const formatDate = (date, format = userPreferedDateFormat) => {
        return moment(date).formatPHP(format);
    };
    const formatDateWithTimezone = (date, timezone = userPreferedTimezone, format = userPreferedDateFormat) => {
        return formatDate(convertDateToTimezone(date, timezone), format);
    };

    eZ.addConfig('helpers.timezone', {
        convertDateToTimezone,
        formatDate,
        formatDateWithTimezone,
    });
})(window, document, window.eZ, window.moment);
