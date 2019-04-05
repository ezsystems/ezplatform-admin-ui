import EzConfigBase from './base';

export default class EzInlineCustomTagConfig extends EzConfigBase {
    constructor(config) {
        super(config);

        const editButton = !!config.alloyEditor.attributes ? `${config.name}edit` : '';
        const defaultButtons = [editButton, 'ezblockremove'];
        const customButtons = config.alloyEditor.toolbarButtons;
        const buttons = customButtons && customButtons.length ? customButtons : defaultButtons;

        this.name = config.name;
        this.buttons = buttons;

        this.addExtraButtons(config.extraButtons);

        this.test = this.test.bind(this);
    }

    /**
     * Tests whether the `inline custom tag` toolbar should be visible, it is visible
     * when an ezinlinecustomtag widget gets the focus.
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
        const path = payload.editor.get('nativeEditor').elementPath();
        const isInlineCustomTag = path.contains(
            (element) => element.$.dataset.ezelement === 'eztemplateinline' && element.$.dataset.ezname === this.name
        );

        return !!((element && element.$.dataset.ezname === this.name) || isInlineCustomTag);
    }
}

eZ.addConfig('ezAlloyEditor.ezInlineCustomTagConfig', EzInlineCustomTagConfig);
