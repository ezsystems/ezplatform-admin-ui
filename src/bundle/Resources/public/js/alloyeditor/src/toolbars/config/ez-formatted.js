import EzConfigBase from './base';

export default class EzFormattedConfig extends EzConfigBase {
    constructor(config) {
        super(config);

        this.name = 'formatted';
        this.buttons = [
            'ezmoveup',
            'ezmovedown',
            this.getEditAttributesButton(config),
            this.getStyles(config.customStyles),
            'ezanchor',
            'ezblockremove',
        ];

        this.addExtraButtons(config.extraButtons);
    }

    /**
     * Tests whether the `pre` toolbar should be visible. It is
     * visible when the selection is empty and when the caret is inside a
     * formatted tag (<pre>).
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
        const nativeEditor = payload.editor.get('nativeEditor');
        const path = nativeEditor.elementPath();

        return nativeEditor.isSelectionEmpty() && path && path.contains('pre');
    }
}

eZ.addConfig('ezAlloyEditor.ezFormattedConfig', EzFormattedConfig);
