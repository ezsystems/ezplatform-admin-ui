import AlloyEditor from 'alloyeditor';

export default class EzTextConfig {
    constructor(config) {
        this.name = 'text';
        this.buttons = [
            this.getStyles(config.customStyles),
            'ezbold',
            'ezitalic',
            'ezunderline',
            'ezsubscript',
            'ezsuperscript',
            'ezquote',
            'ezstrike',
            'ezlink',
            ...config.inlineCustomTags,
        ];

        this.test = AlloyEditor.SelectionTest.text;
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
}

eZ.addConfig('ezAlloyEditor.ezTextConfig', EzTextConfig);
