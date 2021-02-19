(function(global, doc, eZ, Chart) {
    const IBEXA_WHITE = '#fff';
    const IBEXA_COLOR_BASE = '#e0e0e8';
    const IBEXA_COLOR_BASE_DARK = '#878b90';

    class BaseChart {
        constructor(data) {
            this.setData(data);
            this.lang = document.documentElement.lang.replace('_', '-'); // TO DO: Get this config from settings
        }

        setData(data) {
            this.datasets = data.datasets;
            this.labels = data.labels;
        }

        getType() {}

        getLayoutOptions() {}

        getScaleOptions() {}

        getLegendOptions(chart) {}

        callbackAfterRender() {}

        updateChartMessageDisplay() {
            const chartBody = this.chart.canvas.closest('.ez-chart__body');
            const chartMessagesNode = chartBody.querySelector('.ez-chart__message');
            const chartMessageMethod = this.chart.config.data.datasets.length ? 'add' : 'remove';

            chartMessagesNode.classList[chartMessageMethod]('d-none');
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
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: this.getLayoutOptions(),
                    elements: {
                        point: {
                            radius: 2,
                        },
                        line: {
                            tension: 0,
                        },
                    },
                    legendCallback: (chart) => this.getLegendOptions(chart),
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
                            labelTextColor: (tooltipItem, chart) => {
                                return IBEXA_COLOR_BASE_DARK;
                            },
                        },
                    },
                    animation: {
                        onComplete: (animation) => {
                            const chart = animation.chart;
                            const chartMethod = chart.config.data.datasets.length ? 'remove' : 'add';
                            const chartNode = chart.canvas.closest('.ez-chart');

                            chartNode.dispatchEvent(new CustomEvent('ez-chart-animation-complete'));
                            chartNode.classList[chartMethod]('ez-chart--no-data');
                        },
                    },
                    scales: this.getScaleOptions(),
                },
            });

            this.updateChartMessageDisplay();
            this.callbackAfterRender();
        }
    }

    eZ.addConfig('core.BaseChart', BaseChart);
})(window, window.document, window.eZ, window.Chart);
