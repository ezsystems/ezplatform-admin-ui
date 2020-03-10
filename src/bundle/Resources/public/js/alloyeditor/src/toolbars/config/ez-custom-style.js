import EzConfigBase from './base';

export default class EzCustomStyleConfig extends EzConfigBase {
    constructor(config) {
        super(config);

        this.name = 'custom-style';
        this.buttons = [
            'ezmoveup',
            'ezmovedown',
            this.getStyles(config.customStyles),
            'ezanchor',
            'ezblocktextalignleft',
            'ezblocktextaligncenter',
            'ezblocktextalignright',
            'ezblocktextalignjustify',
            'ezblockremove',
        ];

        this.addExtraButtons(config.extraButtons);
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
        const isInTable = path && path.contains((element) => element.is('table'));

        return (
            nativeEditor.isSelectionEmpty() &&
            path &&
            path.contains((element) => {
                const ezElement = element.getAttribute('data-ezelement');

                return (
                    (ezElement === 'eztemplate' || ezElement === 'eztemplateinline') &&
                    element.getAttribute('data-eztype') === 'style' &&
                    !isInTable
                );
            })
        );
    }
}

eZ.addConfig('ezAlloyEditor.ezCustomStyleConfig', EzCustomStyleConfig);
