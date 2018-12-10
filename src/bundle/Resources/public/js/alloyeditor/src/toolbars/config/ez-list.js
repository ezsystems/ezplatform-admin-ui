import EzConfigBase from './base';

export default class EzListConfig extends EzConfigBase {
    constructor(config) {
        super(config);

        this.name = 'list';
        this.buttons = ['ezmoveup', 'ezmovedown', this.getStyles(config.customStyles), 'ezembedinline', 'ezblockremove'];
    }

    getStyles(customStyles = []) {
        return {
            name: 'styles',
            cfg: {
                showRemoveStylesItem: true,
                styles: [...customStyles],
            },
        };
    }

    /**
     * Tests whether the `list` toolbar should be visible. It is
     * visible when the selection is empty and when the caret is inside a
     * list.
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

        return nativeEditor.isSelectionEmpty() && path && (path.contains('ul') || path.contains('ol'));
    }
}
