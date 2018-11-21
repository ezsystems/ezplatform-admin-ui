import EzConfigBase from './base';

export default class EzEmbedImageLinkConfig extends EzConfigBase {
    constructor() {
        super();

        this.name = 'embedimagelink';
        this.buttons = ['ezimagelink'];
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
        const target = new CKEDITOR.dom.element(nativeEvent.target);
        const widget = payload.editor.get('nativeEditor').widgets.getByElement(target);

        return !!(widget && widget.name === 'ezembed' && widget.isImage() && widget.isEditingLink());
    }
}
