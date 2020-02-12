import EzConfigTableBase from './base-table';

export default class EzTableCellConfig extends EzConfigTableBase {
    constructor(config) {
        super(config);

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
    }

    getConfigName() {
        return 'td';
    }

    test(payload) {
        const nativeEditor = payload.editor.get('nativeEditor');
        const path = nativeEditor.elementPath();

        return path && path.lastElement.is('td');
    }
}

eZ.addConfig('ezAlloyEditor.ezTableCellConfig', EzTableCellConfig);
