import EzConfigBase from './base';

export default class EzEmbedImageConfig extends EzConfigBase {
    constructor(config) {
        super(config);

        this.name = 'embedimage';
        this.buttons = [
            'ezmoveup',
            'ezmovedown',
            this.getEditAttributesButton(config),
            'ezimageupdate',
            'ezimagevariation',
            'ezimagelink',
            'ezanchor',
            'ezembedleft',
            'ezembedcenter',
            'ezembedright',
            'ezblockremove',
        ];

        this.addExtraButtons(config.extraButtons);
    }

    /**
     * Tests whether the `image` toolbar should be visible, it is visible
     * when an ezembed widget containing an <img> is visible.
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

        return !!(widget && widget.name === 'ezembed' && widget.isImage());
    }
}

eZ.addConfig('ezAlloyEditor.ezEmbedImageConfig', EzEmbedImageConfig);
