(function(global, doc, eZ) {
    class PieChart extends eZ.core.BaseChart {
        constructor(data) {
            super(data);

            this.type = 'pie';
        }

        getType() {
            return this.type;
        }
    }

    eZ.addConfig('core.chart.PieChart', PieChart);
})(window, window.document, window.eZ, window.Chart);
