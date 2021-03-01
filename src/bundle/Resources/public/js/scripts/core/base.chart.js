(function(global, doc, eZ, Chart) {
    const IBEXA_WHITE = '#fff';
    const IBEXA_COLOR_BASE = '#e0e0e8';
    const IBEXA_COLOR_BASE_DARK = '#878b90';
    const MIN_ANGLE_TO_SHOW_DATA_LABEL = 0.1;
    const BOX_HEIGHT = 10;
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
    const dataLabelPlugin = {
        calculateCords: (ctxElement) => {
            const { startAngle, endAngle, outerRadius, innerRadius, x, y } = ctxElement;
            const diffAngle = endAngle - startAngle;
            const centerAngle = startAngle + diffAngle / 2;
            const rangeFromCenter = (outerRadius - innerRadius) / 2 + innerRadius;
            const xPos = x + Math.cos(centerAngle) * rangeFromCenter;
            const yPos = y + Math.sin(centerAngle) * rangeFromCenter;

            return { xPos, yPos, startAngle, endAngle, diffAngle };
        },
        beforeRender: (chart) => {
            if (chart.config.options.showDataLabel) {
                const { datasets } = chart.config.data;

                chart.pluginTooltips = datasets.map((dataset, datasetsIndex) => {
                    return chart.getDatasetMeta(datasetsIndex).data.map((activeSector, activeSectorIndex) => {
                        return new Chart.Tooltip(
                            {
                                dataValue: chart.data.datasets[datasetsIndex].data[activeSectorIndex],
                                _options: chart.options.tooltips,
                                _active: activeSector,
                            },
                            chart
                        );
                    });
                });
            }
        },
        afterDraw: (chart) => {
            if (chart.config.options.showDataLabel) {
                chart.pluginTooltips.forEach((pluginTooltip) => {
                    pluginTooltip.forEach((tooltipData) => {
                        const { hidden } = tooltipData._active;
                        const cords = dataLabelPlugin.calculateCords(tooltipData._active._view);

                        if (!hidden && cords.diffAngle >= MIN_ANGLE_TO_SHOW_DATA_LABEL) {
                            const textCords = chart.ctx.measureText(tooltipData.dataValue);
                            const boxWidth = textCords.width * 1.5;

                            tooltipData.initialize();
                            tooltipData._options.caretPadding = 20;
                            tooltipData.update();

                            chart.ctx.textAlign = 'center';
                            chart.ctx.textBaseline = 'middle';
                            chart.ctx.font = 'normal 12px';
                            chart.ctx.fillStyle = IBEXA_WHITE;
                            chart.ctx.fillText(tooltipData.dataValue, cords.xPos, cords.yPos);
                            chart.ctx.lineJoin = 'round';
                            chart.ctx.lineWidth = BOX_HEIGHT;
                            chart.ctx.strokeStyle = 'rgba(255, 255, 255, 0.3)';
                            chart.ctx.strokeRect(cords.xPos - boxWidth / 2, cords.yPos - BOX_HEIGHT / 2, boxWidth, BOX_HEIGHT);
                        }
                    });
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
                animation: {
                    onComplete: (animation) => this.onCompleteAnimationCallback(animation),
                },
                ...options,
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
