export default class EzTableCellConfig {
    constructor(config) {
        this.name = 'td';

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

    test(payload) {
        const nativeEditor = payload.editor.get('nativeEditor');
        const path = nativeEditor.elementPath();

        return path && path.lastElement.is('td');
    }
}

eZ.addConfig('ezAlloyEditor.ezTableCellConfig', EzTableCellConfig);
