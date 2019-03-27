(function(global, doc, eZ, moment) {
    const userPreferedTimezone = eZ.adminUiConfig.timezone;
    const userPreferedFullDateTimeFormat = eZ.adminUiConfig.dateFormat.full_datetime;
    const userPreferedFullDateFormat = eZ.adminUiConfig.dateFormat.full_date;
    const userPreferedFullTimeFormat = eZ.adminUiConfig.dateFormat.full_time;
    const userPreferedShortDateTimeFormat = eZ.adminUiConfig.dateFormat.short_datetime;
    const userPreferedShortDateFormat = eZ.adminUiConfig.dateFormat.short_date;
    const userPreferedShortTimeFormat = eZ.adminUiConfig.dateFormat.short_time;

    const convertDateToTimezone = (date, timezone = userPreferedTimezone, forceSameTime = false) => {
        return moment(date).tz(timezone, forceSameTime);
    };
    const formatDate = (date, format, timezone = null) => {
        if (timezone) {
            date = convertDateToTimezone(date, timezone);
        }

        return moment(date).formatICU(format);
    };
    const formatFullDateTime = (date, timezone = null, format = userPreferedFullDateTimeFormat) => {
        return formatDate(date, format, timezone);
    };
    const formatFullDate = (date, timezone = null, format = userPreferedFullDateFormat) => {
        return formatDate(date, format, timezone);
    };
    const formatFullTime = (date, timezone = null, format = userPreferedFullTimeFormat) => {
        return formatDate(date, format, timezone);
    };
    const formatShortDateTime = (date, timezone = null, format = userPreferedShortDateTimeFormat) => {
        return formatDate(date, format, timezone);
    };
    const formatShortDate = (date, timezone = null, format = userPreferedShortDateFormat) => {
        return formatDate(date, format, timezone);
    };
    const formatShortTime = (date, timezone = null, format = userPreferedShortimeFormat) => {
        return formatDate(date, format, timezone);
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
    const deprecatedFormatDateWithTimezone = (date, timezone, format) => {
        console.warn('[DEPRECATED] formatDateWithTimezone function is deprecated');
        console.warn('[DEPRECATED] it will be removed from ezplatform-admin-ui 2.0');
        console.warn('[DEPRECATED] use formatFullDateTime instead');

        return formatFullDateTime(date, timezone, format);
    };
    const deprecatedFormatShortDateWithTimezone = (date, timezone, format) => {
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
