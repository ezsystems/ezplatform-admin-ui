import EzConfigBase from './base';

export default class EzCustomTagConfig extends EzConfigBase {
    constructor(config) {
        super(config);

        const defaultButtons = [
            'ezmoveup',
            'ezmovedown',
            `${config.name}edit`,
            'ezanchor',
            'ezembedleft',
            'ezembedcenter',
            'ezembedright',
            'ezblockremove',
        ];
        const customButtons = config.alloyEditor.toolbarButtons;
        const buttons = customButtons && customButtons.length ? customButtons : defaultButtons;

        this.name = config.name;
        this.buttons = buttons;

        this.test = this.test.bind(this);
    }

    /**
     * Tests whether the `embed` toolbar should be visible, it is visible
     * when an ezembed widget gets the focus.
     *
     * @method test
     * @param {Object} payload
     * @param {AlloyEditor.Core} payload.editor
     * @param {Object} payload.data
     * @param {Object} payload.data.selectionData
     * @param {Event} payload.data.nativeEvent
     * @return {Boolean}
     */
    test(payload) {
        const element = payload.data.selectionData.element;

        return !!(element && element.$.dataset.ezname === this.name);
    }
}

eZ.addConfig('ezAlloyEditor.ezCustomTagConfig', EzCustomTagConfig);
