import EzConfigBase from './base';

const EMBED_INLINE_NAME = 'ezembedinline';

export default class EzEmbedInlineConfig extends EzConfigBase {
    constructor(config) {
        super(config);

        this.name = 'embedinline';
        this.buttons = ['ezembedupdate', 'ezblockremove'];

        this.addExtraButtons(config.extraButtons);
    }

    /**
     * Tests whether the `embedinline` toolbar should be visible, it is visible
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
        const nativeEvent = payload.data.nativeEvent;

        if (!nativeEvent) {
            return false;
        }

        const target = new CKEDITOR.dom.element(nativeEvent.target);
        const widget = payload.editor.get('nativeEditor').widgets.getByElement(target);

        return !!(widget && widget.name === EMBED_INLINE_NAME);
    }
}

eZ.addConfig('ezAlloyEditor.ezEmbedInlineConfig', EzEmbedInlineConfig);
