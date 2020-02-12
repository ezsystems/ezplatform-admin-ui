export default class EzTableHeaderConfig {
    constructor(config) {
        this.name = 'th';

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

        return path && path.lastElement.is('th');
    }
}

eZ.addConfig('ezAlloyEditor.ezTableHeaderConfig', EzTableHeaderConfig);
