(function(moment) {
    /*
        ([yqLdDeEcaZ])\1* -> find any pattern of one or repeated one of these characters
        or
        \'([^\']|(\'\'))*\' -> find any string in ' ' quotes
    */
    const formatICUEx = /([yqLdDeEcaZ])\1*|'([^']|(''))*'/g;
    /*
        Allowed formats:
            y, yy, yyyy, Y, YY, YYYY,
            q, Q,
            M, MM, MMM, MMMM, L, LL, LLL, LLLL,
            w, WW,
            d, dd,
            D, DDD,
            E, EE, EEE, EEEE, EEEEEE, e, ee, eee, eeee, eeeeee, c, cc, ccc, cccc, cccccc,
            a,
            h, hh, H, HH, k, kk,
            m, mm,
            s, ss, S...,
            Z, ZZ, ZZZ, ZZZZZ
    */
    const formatICUMap = {
        y: 'Y',
        yy: 'YY',
        yyyy: 'YYYY',
        q: 'Q',
        L: 'M',
        LL: 'MM',
        LLL: 'MMM',
        LLLL: 'MMMM',
        dd: 'DD',
        d: 'D',
        D: 'DDD',
        DDD: 'DDDD',
        eeeeee: 'dd',
        eeee: 'dddd',
        eee: 'ddd',
        ee: 'E',
        e: 'E',
        EEEEEE: 'dd',
        EEEE: 'dddd',
        EEE: 'ddd',
        EE: 'ddd',
        E: 'ddd',
        cccccc: 'dd',
        cccc: 'dddd',
        ccc: 'ddd',
        cc: 'E',
        c: 'E',
        a: 'A',
        Z: 'ZZ',
        ZZ: 'ZZ',
        ZZZ: 'ZZ',
        ZZZZ: 'Z',
    };
    const formatEscapedString = function(icuStr) {
        if (icuStr === '\'\'') {
            return '[\']';
        }

        return icuStr.replace(/'(.*)'/g, '[$1]').replace(/''/g, '\'');
    };

    moment.fn.formatICU = function(format) {
        const form = format.replace(formatICUEx, (icuStr) => {
            if (icuStr[0] === '\'') {
                return formatEscapedString(icuStr);
            }

            if (formatICUMap[icuStr] === undefined) {
                return icuStr;
            }

            return typeof formatICUMap[icuStr] === 'function' ? formatICUMap[icuStr].call(this) : formatICUMap[icuStr];
        });

        return this.format(form);
    };
})(window.moment);
