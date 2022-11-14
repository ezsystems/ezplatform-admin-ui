(function(moment, eZ) {
    const { backOfficeLanguage } = eZ.adminUiConfig;

    moment.locale(backOfficeLanguage);

    /*
        ([yqLdDeEcaZ])\1* -> find any pattern of one or repeated one of these characters
        or
        \'([^\']|(\'\'))*\' -> find any string in ' ' quotes
    */
    const formatICUEx = /([yqLdDeEcaZ])\1*|\'([^\']|(\'\'))*\'/g;
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
    const formatPHPEx = /[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU]/g;
    const formatPHPMap = {
        d: 'DD',
        D: 'ddd',
        j: 'D',
        l: 'dddd',
        N: 'E',
        S: function() {
            return `[${this.format('Do').replace(/\d*/g, '')}]`;
        },
        w: 'd',
        z: function() {
            return this.format('DDD') - 1;
        },
        W: 'W',
        F: 'MMMM',
        m: 'MM',
        M: 'MMM',
        n: 'M',
        t: function() {
            return this.daysInMonth();
        },
        L: function() {
            return this.isLeapYear() ? 1 : 0;
        },
        o: 'GGGG',
        Y: 'YYYY',
        y: 'YY',
        a: 'a',
        A: 'A',
        B: function() {
            var thisUTC = this.clone().utc(),
                swatch = ((thisUTC.hours() + 1) % 24) + thisUTC.minutes() / 60 + thisUTC.seconds() / 3600;
            return Math.floor((swatch * 1000) / 24);
        },
        g: 'h',
        G: 'H',
        h: 'hh',
        H: 'HH',
        i: 'mm',
        s: 'ss',
        u: '[u]',
        e: '[e]',
        I: function() {
            return this.isDST() ? 1 : 0;
        },
        O: 'ZZ',
        P: 'Z',
        T: '[T]',
        Z: function() {
            return parseInt(this.format('ZZ'), 10) * 36;
        },
        c: 'YYYY-MM-DD[T]HH:mm:ssZ',
        r: 'ddd, DD MMM YYYY HH:mm:ss ZZ',
        U: 'X',
    };

    const formatEscapedString = function(icuStr) {
        if (icuStr === '\'\'') {
            return '[\']';
        }

        return icuStr.replace(/\'(.*)\'/g, '[$1]').replace(/\'\'/g, '\'');
    }

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
})(window.moment, window.eZ);
