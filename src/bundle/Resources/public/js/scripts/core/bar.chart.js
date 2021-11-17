(function(global, doc, eZ) {
    const MAX_NUMBER_OF_LABELS = 16;
    const barDefaultOptions = {
        // elements: {
        //     point: {
        //         radius: 2,
        //     },
        //     line: {
        //         tension: 0,
        //     },
        // },
        // scales: {
        //     xAxes: [
        //         {
        //             display: true,
        //             gridLines: {
        //                 display: false,
        //             },
        //             ticks: {
        //                 maxRotation: 0,
        //                 autoSkip: false,
        //                 callback: (value, index, labels) => {
        //                     const labelsInterval = Math.max(Math.ceil(labels.length / MAX_NUMBER_OF_LABELS), 1);
        //                     const shouldDisplayLabel = !(index % labelsInterval);

        //                     return shouldDisplayLabel ? value : null;
        //                 },
        //             },
        //         },
        //     ],
        //     yAxes: [
        //         {
        //             display: true,
        //             type: 'logarithmic',
        //             ticks: {
        //                 callback: (...args) => {
        //                     const value = Chart.Ticks.formatters.logarithmic.call(this, ...args);

        //                     if (value.length) {
        //                         return Number(value).toLocaleString();
        //                     }

        //                     return value;
        //                 },
        //             },
        //         },
        //     ],
        // },
    };

    class BarChart extends eZ.core.BaseChart {
        constructor(data, options = {}) {
            super(data, {
                ...barDefaultOptions,
                ...options,
            });

            this.type = 'bar';
        }

        getType() {
            return this.type;
        }

        setData(data) {
            super.setData(data);

            // this.labelsInterval = Math.max(Math.ceil(this.labels.length / MAX_NUMBER_OF_LABELS), 1);
        }
    }

    eZ.addConfig('core.chart.BarChart', BarChart);
})(window, window.document, window.eZ, window.Chart);
