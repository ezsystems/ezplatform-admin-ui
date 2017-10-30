import AlloyEditor from 'alloyeditor';

export default class EzTableConfig {
    constructor() {
        this.name = 'table';
        this.buttons = [
            'ezmoveup',
            'ezmovedown',
            'tableHeading',
            'tableRow',
            'tableColumn',
            'tableCell',
            'tableRemove',
        ];

        this.getArrowBoxClasses = AlloyEditor.SelectionGetArrowBoxClasses.table;
        this.setPosition = AlloyEditor.SelectionSetPosition.table;
        this.test = AlloyEditor.SelectionTest.table;
    }
}
