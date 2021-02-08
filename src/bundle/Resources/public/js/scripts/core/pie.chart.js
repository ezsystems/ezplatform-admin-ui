(function(global, doc, eZ) {
    class PieChart extends eZ.core.BaseChart {
        constructor(data) {
            super(data);

            this.type = 'pie';
        }

        getType() {
            return this.type;
        }

        legendOptions(chart) {
            const { backgroundColor } = chart.data.datasets[0];
            const { legendCheckboxTemplate } = this.legendContainer.dataset;
            const fragment = doc.createDocumentFragment();

            chart.data.labels.forEach((label, index) => {
                const container = doc.createElement('div');
                const rendredTempalte = legendCheckboxTemplate
                    .replace('{{ checked_color }}', backgroundColor[index])
                    .replace('{{ dataset_index }}', index)
                    .replace('{{ label }}', label);

                container.insertAdjacentHTML('beforeend', rendredTempalte);

                const checkboxNode = container.querySelector('.ez-chart__legend-checkbox-label');
                checkboxNode.querySelector('input').checked = true;

                fragment.append(checkboxNode);
            });

            return fragment;
        }

        setLegendCheckboxes() {
            this.legendContainer.innerHTML = '';
            this.legendContainer.appendChild(this.chart.generateLegend());
            this.legendContainer.querySelectorAll('.ez-input--legend-checkbox').forEach((legendCheckbox) => {
                this.setCheckboxBackground(legendCheckbox);
                legendCheckbox.addEventListener('change', (event) => {
                    const dateValues = [];
                    const backgroundColorValues = [];
                    this.chart.data.datasets[0].data = [];
                    this.chart.data.datasets[0].backgroundColor = [];
                    this.legendContainer.querySelectorAll('.ez-input--legend-checkbox').forEach((legendCheckbox, index) => {
                        if (legendCheckbox.checked) {
                            dateValues.push(this.initValues.datasets[0].data[index]);
                            backgroundColorValues.push(this.initValues.datasets[0].backgroundColor[index]);
                        }
                    });
                    this.chart.data.datasets[0].data = dateValues;
                    this.chart.data.datasets[0].backgroundColor = backgroundColorValues;
                    this.setCheckboxBackground(event.currentTarget);
                    this.chart.update();
                });
            });
        }
    }

    eZ.addConfig('core.chart.PieChart', PieChart);
})(window, window.document, window.eZ, window.Chart);
