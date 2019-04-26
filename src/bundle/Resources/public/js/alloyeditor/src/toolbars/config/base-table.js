import AlloyEditor from 'alloyeditor';

export default class EzConfigTableBase {
    constructor(config) {
        this.name = this.getConfigName();

        const editAttributesButton = config.attributes[this.name] || config.classes[this.name] ? `${this.name}edit` : '';

        this.buttons = [
            'ezmoveup',
            'ezmovedown',
            editAttributesButton,
            'tableHeading',
            'ezembedinline',
            'ezanchor',
            'eztablerow',
            'eztablecolumn',
            'eztablecell',
            'eztableremove',
            ...config.extraButtons[this.name],
        ];

        this.getArrowBoxClasses = AlloyEditor.SelectionGetArrowBoxClasses.table;
        this.setPosition = AlloyEditor.SelectionSetPosition.table;
    }

    getConfigName() {
        return '';
    }
}
