import EzConfigBase from './base';

export default class EzCustomStyleConfig extends EzConfigBase {
    constructor(config) {
        super(config);

        this.name = 'custom-style';
        this.buttons = [
            'ezmoveup',
            'ezmovedown',
            this.getStyles(config.customStyles),
            'ezblocktextalignleft',
            'ezblocktextaligncenter',
            'ezblocktextalignright',
            'ezblocktextalignjustify',
            'ezblockremove',
        ];
    }

    getStyles(customStyles = []) {
        return {
            name: 'styles',
            cfg: {
                showRemoveStylesItem: false,
                styles: [...customStyles],
            },
        };
    }

    /**
     * Tests whether the `custom style` toolbar should be visible. It is
     * visible when an existing custom style gets the focus.
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

        return (
            nativeEditor.isSelectionEmpty() &&
            path &&
            path.contains(function(el) {
                return (
                    el.hasAttribute('data-ezelement') &&
                    (el.getAttribute('data-ezelement') === 'ezstyle' || el.getAttribute('data-ezelement') === 'ezstyleinline')
                );
            })
        );
    }
}
