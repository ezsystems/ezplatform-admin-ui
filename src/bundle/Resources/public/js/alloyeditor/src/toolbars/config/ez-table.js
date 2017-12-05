import AlloyEditor from 'alloyeditor';

export default class EzTableConfig {
    constructor() {
        this.name = 'table';
        this.buttons = [
            'ezmoveup',
            'ezmovedown',
            'tableHeading',
            'eztablerow',
            'eztablecolumn',
            'eztablecell',
            'eztableremove',
        ];

        this.getArrowBoxClasses = AlloyEditor.SelectionGetArrowBoxClasses.table;
        this.setPosition = AlloyEditor.SelectionSetPosition.table;
        this.test = AlloyEditor.SelectionTest.table;
    }
}
