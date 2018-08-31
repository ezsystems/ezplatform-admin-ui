(function(moment) {
    const formatEx = /[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU]/g;
    const formatMap = {
        d: 'DD',
        D: 'ddd',
        j: 'D',
        l: 'dddd',
        N: 'E',
        S: function() {
            return '[' + this.format('Do').replace(/\d*/g, '') + ']';
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

    moment.fn.formatPHP = function(format) {
        return this.format(
            format.replace(formatEx, (phpStr) => {
                return typeof formatMap[phpStr] === 'function' ? formatMap[phpStr].call(this) : formatMap[phpStr];
            })
        );
    };
})(window.moment);
