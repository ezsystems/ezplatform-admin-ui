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
            this.lang = document.documentElement.lang.replace('_', '-'); // TO DO: Get this config from settings
        }

        setData(data) {
            this.datasets = data.datasets;
            this.labels = data.labels;
        }

        setOptions(options) {
            this.options = {
                ...defaultOptions,
                animation: {
                    onComplete: this.onCompleteAnimationCallback,
                },
                ...options,
            };
        }

        getType() {}

        getLayoutOptions() {}

        getScaleOptions() {}

        getLegendOptions() {}

        callbackAfterRender() {}

        updateChartMessageDisplay() {
            const chartBody = this.chart.canvas.closest('.ez-chart__body');
            const chartMessagesNode = chartBody.querySelector('.ez-chart__message');

            chartMessagesNode.classList.toggle('d-none', this.chart.config.data.datasets.length);
        }

        updateChart() {
            this.chart.data.labels = this.labels;
            this.chart.data.datasets = this.datasets;

            this.chart.update();

            this.updateChartMessageDisplay();
            this.callbackAfterRender();
        }

        onCompleteAnimationCallback(animation) {
            const { chart } = animation;
            const chartNode = chart.canvas.closest('.ez-chart');

            chartNode.dispatchEvent(new CustomEvent('ez-chart-animation-complete'));
            chartNode.classList.toggle('ez-chart--no-data', !chart.config.data.datasets.length);
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
