(function(global, doc, eZ, Chart) {
    const OUTER_RADIUS_INCREASE_VALUE = 20;
    const doughnutDefaultOptions = {
        onHover: function(event) {
            const dataset = this.chart.getDatasetMeta(0);

            item = this.chart.getElementAtEvent(event);

            if (item.length) {
                lastActiveItem = item[0];

                dataset.data.forEach((item) => {
                    item._model.outerRadius =
                        item._index === lastActiveItem._index
                            ? this.chart.outerRadius + OUTER_RADIUS_INCREASE_VALUE
                            : this.chart.outerRadius;
                });
            } else {
                if (dataset) {
                    dataset.data.forEach((item) => {
                        item._model.outerRadius = this.chart.outerRadius;
                    });
                }
            }
        },
        layout: {
            padding: {
                top: 25,
                right: 15,
                bottom: 25,
                left: 15,
            },
        },
        elements: {
            arc: {
                borderWidth: 0,
            },
        },
    };

    class DoughnutChart extends eZ.core.BaseChart {
        constructor(data, options = {}) {
            super(data, {
                ...doughnutDefaultOptions,
                ...options,
            });

            this.type = 'doughnut';
        }

        getType() {
            return this.type;
        }
    }

    eZ.addConfig('core.chart.DoughnutChart', DoughnutChart);
})(window, window.document, window.eZ, window.Chart);
