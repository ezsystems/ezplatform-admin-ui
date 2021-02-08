(function(global, doc, eZ) {
    const MAX_NUMBER_OF_LABELS = 16;

    class LineChart extends eZ.core.BaseChart {
        constructor(data) {
            super(data);

            this.type = 'line';
        }

        getType() {
            return this.type;
        }

        setData(data) {
            super.setData(data);

            if (this.labels.length) {
                this.labelsInterval = Math.ceil(this.labels.length / MAX_NUMBER_OF_LABELS);
            } else {
                this.labelsInterval = 1;
            }
        }

        setLegendCheckboxes() {
            this.legendContainer.innerHTML = '';
            this.legendContainer.appendChild(this.chart.generateLegend());

            this.legendContainer.querySelectorAll('.ez-input--legend-checkbox').forEach((legendCheckbox) => {
                this.setCheckboxBackground(legendCheckbox);

                legendCheckbox.addEventListener('change', (event) => {
                    const { datasetIndex } = event.currentTarget.dataset;
                    const dataset = this.chart.data.datasets[datasetIndex];

                    dataset.hidden = !dataset.hidden;

                    this.setCheckboxBackground(event.currentTarget);
                    this.chart.update();
                });
            });
        }

        scaleOptions() {
            return {
                xAxes: [
                    {
                        display: true,
                        gridLines: {
                            display: false,
                        },
                        ticks: {
                            maxRotation: 0,
                            autoSkip: false,
                            callback: (value, index, values) => {
                                return index % this.labelsInterval ? null : value;
                            },
                        },
                    },
                ],
                yAxes: [
                    {
                        display: true,
                        type: 'logarithmic',
                        ticks: {
                            callback: (...args) => {
                                const value = Chart.Ticks.formatters.logarithmic.call(this, ...args);

                                if (value.length) {
                                    return Number(value).toLocaleString();
                                }

                                return value;
                            },
                        },
                    },
                ],
            };
        }

        legendOptions(chart) {
            const { legendCheckboxTemplate } = this.legendContainer.dataset;
            const fragment = doc.createDocumentFragment();

            chart.data.datasets.forEach((dataset, index) => {
                const container = doc.createElement('div');
                const rendredTempalte = legendCheckboxTemplate
                    .replace('{{ checked_color }}', dataset.backgroundColor)
                    .replace('{{ dataset_index }}', index)
                    .replace('{{ label }}', dataset.label);

                dataset.hidden = false;
                container.insertAdjacentHTML('beforeend', rendredTempalte);

                const checkboxNode = container.querySelector('.ez-chart__legend-checkbox-label');

                checkboxNode.querySelector('input').checked = !dataset.hidden;
                fragment.append(checkboxNode);
            });

            return fragment;
        }
    }

    eZ.addConfig('core.chart.LineChart', LineChart);
})(window, window.document, window.eZ, window.Chart);
