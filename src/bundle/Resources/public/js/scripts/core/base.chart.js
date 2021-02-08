(function(global, doc, eZ, Chart) {
    class BaseChart {
        constructor(data) {
            this.setData(data);
            this.lang = document.documentElement.lang.replace('_', '-'); // TO DO: Get this config from settings
        }

        setData(data) {
            this.datasets = data.datasets;
            this.labels = data.labels;

            if (data.summary) {
                this.summary = data.summary;
            }
        }

        setCheckboxBackground(checkbox) {
            const { checkedColor } = checkbox.dataset;
            const { checked } = checkbox;

            if (checked) {
                checkbox.style.backgroundColor = checkedColor;
                checkbox.style.borderColor = checkedColor;
            } else {
                checkbox.style.backgroundColor = '#fff';
                checkbox.style.borderColor = '#878b90';
            }
        }

        setLegendCheckboxes() {}

        getChartType() {}

        getSummaryValue() {}

        getSummaryName(value) {
            return value.name;
        }

        updateSummary() {}

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

            if (this.legendContainer) {
                this.setLegendCheckboxes();
            }

            if (this.summaryContainer) {
                this.updateSummary();
            }
        }

        layoutOptions() {}

        scaleOptions() {}

        legendOptions(chart) {}

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
                    layout: this.layoutOptions(),
                    elements: {
                        point: {
                            radius: 2,
                        },
                        line: {
                            tension: 0,
                        },
                    },
                    legendCallback: (chart) => this.legendOptions(chart),
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        enabled: true,
                        mode: 'nearest',
                        cornerRadius: 4,
                        borderWidth: 1,
                        borderColor: '#e0e0e8',
                        titleFontStyle: 'light',
                        titleFontColor: '#878b90',
                        xPadding: 12,
                        yPadding: 12,
                        backgroundColor: '#fff',
                        callbacks: {
                            labelTextColor: (tooltipItem, chart) => {
                                return '#878b90';
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
                    scales: this.scaleOptions(),
                },
            });

            this.updateChartMessageDisplay();

            if (this.legendContainer) {
                this.setLegendCheckboxes();
            }

            if (this.summaryContainer) {
                this.updateSummary();
            }
        }
    }

    eZ.addConfig('core.BaseChart', BaseChart);
})(window, window.document, window.eZ, window.Chart);
