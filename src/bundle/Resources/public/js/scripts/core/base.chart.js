(function(global, doc, eZ, Chart) {
    const IBEXA_WHITE = '#fff';
    const IBEXA_COLOR_BASE = '#e0e0e8';
    const IBEXA_COLOR_BASE_DARK = '#878b90';
    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            display: false,
        },
        tooltips: {
            enabled: true,
            mode: 'nearest',
            cornerRadius: 4,
            borderWidth: 1,
            borderColor: IBEXA_COLOR_BASE,
            titleFontStyle: 'light',
            titleFontColor: IBEXA_COLOR_BASE_DARK,
            xPadding: 12,
            yPadding: 12,
            backgroundColor: IBEXA_WHITE,
            callbacks: {
                labelTextColor: () => {
                    return IBEXA_COLOR_BASE_DARK;
                },
            },
        },
    };

    class BaseChart {
        constructor(data, options = {}) {
            this.setData(data);
            this.setOptions(options);
            this.lang = document.documentElement.lang.replace('_', '-'); // TODO: Get this config from settings
        }

        setData(data) {
            this.datasets = data.datasets;
            this.labels = data.labels;
        }

        setOptions(options) {
            this.options = {
                ...defaultOptions,
                ...options,
            };
        }

        getType() {}

        getLayoutOptions() {}

        getScaleOptions() {}

        getLegendOptions() {}

        callbackAfterRender() {}

        updateChartMessageDisplay() {
            const chartBody = this.chart.canvas.closest('.ibexa-chart__body');
            const chartMessagesNode = chartBody.querySelector('.ibexa-chart__message');

            chartMessagesNode.classList.toggle('d-none', this.chart.config.data.datasets.length);
        }

        updateChart() {
            this.chart.data.labels = this.labels;
            this.chart.data.datasets = this.datasets;

            this.chart.update();

            this.updateChartMessageDisplay();
            this.callbackAfterRender();
        }

        render() {
            this.chart = new Chart(this.canvas.getContext('2d'), {
                type: this.getType(),
                data: {
                    labels: this.labels,
                    datasets: this.datasets,
                },
                options: this.options,
            });

            this.updateChartMessageDisplay();
            this.callbackAfterRender();
        }
    }

    eZ.addConfig('core.BaseChart', BaseChart);
})(window, window.document, window.eZ, window.Chart);
