import AlloyEditor from 'alloyeditor';
import EzConfgiFixedBase from './base-fixed';

export default class EzConfigTableBase extends EzConfgiFixedBase {
    constructor(config) {
        super(config);

        this.name = this.getConfigName();

        const editAttributesButton = config.attributes[this.name] || config.classes[this.name] ? `${this.name}edit` : '';

        this.buttons = [
            'ezmoveup',
            'ezmovedown',
            editAttributesButton,
            'tableHeading',
            'ezembedinline',
            'ezanchor',
            'eztableremove',
            ...config.extraButtons[this.name],
        ];

        this.getArrowBoxClasses = AlloyEditor.SelectionGetArrowBoxClasses.table;
        this.setPosition = AlloyEditor.SelectionSetPosition.table;
    }

    getConfigName() {
        return '';
    }

    getArrowBoxClasses() {
        return 'ae-toolbar-floating';
    }
}
