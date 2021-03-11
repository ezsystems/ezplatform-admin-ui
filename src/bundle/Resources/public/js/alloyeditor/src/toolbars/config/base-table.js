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
    }

    getConfigName() {
        return '';
    }

    getArrowBoxClasses() {
        return 'ae-toolbar-floating';
    }
}
