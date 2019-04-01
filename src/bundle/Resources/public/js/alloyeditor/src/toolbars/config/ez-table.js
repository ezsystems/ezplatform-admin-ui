import AlloyEditor from 'alloyeditor';

export default class EzTableConfig {
    constructor(config) {
        this.name = 'table';
        this.buttons = [
            'ezmoveup',
            'ezmovedown',
            'tableHeading',
            'ezembedinline',
            'ezanchor',
            'eztablerow',
            'eztablecolumn',
            'eztablecell',
            'eztableremove',
            ...config.extraButtons[this.name]
        ];

        this.getArrowBoxClasses = AlloyEditor.SelectionGetArrowBoxClasses.table;
        this.setPosition = AlloyEditor.SelectionSetPosition.table;
        this.test = AlloyEditor.SelectionTest.table;
    }
}

eZ.addConfig('ezAlloyEditor.ezTableConfig', EzTableConfig);
