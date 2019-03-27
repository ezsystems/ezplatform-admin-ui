(function(global, doc, eZ, moment) {
    const userPreferedTimezone = eZ.adminUiConfig.timezone;
    const userPreferedFullDateTimeFormat = eZ.adminUiConfig.dateFormat.fullDateTime;
    const userPreferedShortDateTimeFormat = eZ.adminUiConfig.dateFormat.shortDateTime;

    const convertDateToTimezone = (date, timezone = userPreferedTimezone, forceSameTime = false) => {
        return moment(date).tz(timezone, forceSameTime);
    };
    const formatDate = (date, timezone = null, format) => {
        if (timezone) {
            date = convertDateToTimezone(date, timezone);
        }

        return moment(date).formatICU(format);
    };
    const formatFullDateTime = (date, timezone = null, format = userPreferedFullDateTimeFormat) => {
        return formatDate(date, timezone, format);
    };
    const formatShortDateTime = (date, timezone = null, format = userPreferedShortDateTimeFormat) => {
        return formatDate(date, timezone, format);
    };

    const deprecatedFormatDate = (date, format = userPreferedFullDateTimeFormat) => {
        console.warn('[DEPRECATED] formatDate function is deprecated');
        console.warn('[DEPRECATED] it will change behaviour from ezplatform-admin-ui 2.0');
        console.warn('[DEPRECATED] use formatFullDateTime instead');

        return formatFullDateTime(date, null, format);
    };
    const deprecatedFormatShortDate = (date, format = userPreferedShortDateTimeFormat) => {
        console.warn('[DEPRECATED] formatShortDate function is deprecated');
        console.warn('[DEPRECATED] it will change behaviour from ezplatform-admin-ui 2.0');
        console.warn('[DEPRECATED] use formatShortDateTime instead');

        return formatShortDateTime(date, null, format);
    };
    const deprecatedFormatDateWithTimezone = (date, timezone = userPreferedTimezone, format = userPreferedFullDateTimeFormat) => {
        console.warn('[DEPRECATED] formatDateWithTimezone function is deprecated');
        console.warn('[DEPRECATED] it will be removed from ezplatform-admin-ui 2.0');
        console.warn('[DEPRECATED] use formatFullDateTime instead');

        return formatFullDateTime(date, timezone, format);
    };
    const deprecatedFormatShortDateWithTimezone = (date, timezone = userPreferedTimezone, format = userPreferedShortDateTimeFormat) => {
        console.warn('[DEPRECATED] formatShortDateWithTimezone function is deprecated');
        console.warn('[DEPRECATED] it will be removed from ezplatform-admin-ui 2.0');
        console.warn('[DEPRECATED] use formatShortDateTime instead');

        return formatShortDateTime(date, timezone, format);
    };

    eZ.addConfig('helpers.timezone', {
        convertDateToTimezone,
        formatFullDateTime,
        formatShortDateTime,
        formatDate: deprecatedFormatDate,
        formatShortDate: deprecatedFormatShortDate,
        formatDateWithTimezone: deprecatedFormatDateWithTimezone,
        formatShortDateWithTimezone: deprecatedFormatShortDateWithTimezone,
    });
})(window, document, window.eZ, window.moment);
