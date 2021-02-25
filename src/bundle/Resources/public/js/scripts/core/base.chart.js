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
                labelTextColor: (tooltipItem, chart) => {
                    return IBEXA_COLOR_BASE_DARK;
                },
            },
        },
    };
    const calculateCords = (ctxElement) => {
        const centerAngle = ctxElement.startAngle + (ctxElement.endAngle - ctxElement.startAngle) / 2;
        const rangeFromCenter = (ctxElement.outerRadius - ctxElement.innerRadius) / 2 + ctxElement.innerRadius;
        const x = ctxElement.x + Math.cos(centerAngle) * rangeFromCenter;
        const y = ctxElement.y + Math.sin(centerAngle) * rangeFromCenter;

        return { x, y };
    };
    const dataLabelPlugin = {
        beforeRender: function(chart) {
            if (chart.config.options.showDataLabel) {
                chart.pluginTooltips = [];

                chart.config.data.datasets.forEach(function(dataset, i) {
                    chart.getDatasetMeta(i).data.forEach(function(sector, j) {
                        chart.pluginTooltips.push(
                            new Chart.Tooltip(
                                {
                                    _chart: chart.chart,
                                    _chartInstance: chart,
                                    _data: chart.data,
                                    _options: chart.options.tooltips,
                                    _active: [sector],
                                },
                                chart
                            )
                        );
                    });
                });
            }
        },
        afterDraw: function(chart, easing) {
            if (chart.config.options.showDataLabel) {
                const minAngelToShowDataLabel = 0.1;

                Chart.helpers.each(chart.pluginTooltips, function(tooltip, i) {
                    const ctxActiveDataset = tooltip._active[0];
                    const { startAngle, endAngle, x, y } = ctxActiveDataset._model;
                    const ctxActiveDatasetAngel = endAngle - startAngle;

                    if (!ctxActiveDataset.hidden && ctxActiveDatasetAngel >= minAngelToShowDataLabel) {
                        const dataset = tooltip._data.datasets[0];
                        const textCords = chart.ctx.measureText(dataset.data[i]);
                        const boxWidth = textCords.width * 1.5;
                        const boxHeight = 10;
                        const cornerRadius = 5;
                        const cords = calculateCords(ctxActiveDataset._model);

                        tooltip.initialize();
                        tooltip._options.caretPadding = 20;
                        tooltip.update();

                        chart.ctx.textAlign = 'center';
                        chart.ctx.textBaseline = 'middle';
                        chart.ctx.font = 'normal 12px';
                        chart.ctx.fillStyle = '#fff';
                        chart.ctx.fillText(dataset.data[i], cords.x, cords.y);

                        chart.ctx.lineJoin = 'round';
                        chart.ctx.lineWidth = boxHeight;
                        chart.ctx.strokeStyle = 'rgba(255, 255, 255, 0.3)';

                        chart.ctx.strokeRect(cords.x - boxWidth / 2, cords.y - boxHeight / 2, boxWidth, boxHeight);
                    }
                });
            }
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
                ...options,
                ...{
                    animation: {
                        onComplete: (animation) => this.onCompleteAnimationCallback(animation),
                    },
                },
            };
        }

        getType() {}

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

        onCompleteAnimationCallback(animation) {
            const chart = animation.chart;
            const chartMethod = chart.config.data.datasets.length ? 'remove' : 'add';
            const chartNode = chart.canvas.closest('.ez-chart');

            chartNode.dispatchEvent(new CustomEvent('ez-chart-animation-complete'));
            chartNode.classList[chartMethod]('ez-chart--no-data');
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
    Chart.plugins.register(dataLabelPlugin);
})(window, window.document, window.eZ, window.Chart);
