import AlloyEditor from 'alloyeditor';

export default class EzTextConfig  {
    constructor() {
        this.name = 'text';
        this.buttons = [
            'ezbold',
            'ezitalic',
            'ezunderline',
            'ezsubscript',
            'ezsuperscript',
            'ezquote',
            'ezstrike',
            'ezlink',
        ];

        this.test = AlloyEditor.SelectionTest.text;
    }
}
