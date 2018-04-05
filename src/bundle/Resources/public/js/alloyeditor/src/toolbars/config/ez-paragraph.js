import EzConfigBase from './base';

export default class EzParagraphConfig extends EzConfigBase {
    constructor() {
        super();

        this.name = 'paragraph';
        this.buttons = [
            'ezmoveup',
            'ezmovedown',
            this.getStyles(),
            'ezblocktextalignleft',
            'ezblocktextaligncenter',
            'ezblocktextalignright',
            'ezblocktextalignjustify',
            'ezblockremove',
        ];
    }

    /**
     * Tests whether the `paragraph` toolbar should be visible. It is
     * visible when the selection is empty and when the caret is inside a
     * paragraph.
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

        return (nativeEditor.isSelectionEmpty() && path && path.contains('p'));
    }
}
