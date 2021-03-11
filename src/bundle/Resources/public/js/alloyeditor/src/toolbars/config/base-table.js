import AlloyEditor from 'alloyeditor';

export default class EzConfgiFixedBase {
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
