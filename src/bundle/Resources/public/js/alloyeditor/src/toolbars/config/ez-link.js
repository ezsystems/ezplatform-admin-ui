import AlloyEditor from 'alloyeditor';

export default class EzLinkConfig {
    constructor(config) {
        this.name = 'link';
        this.buttons = ['ezlinkedit', ...config.extraButtons[this.name]];

        this.test = AlloyEditor.SelectionTest.link;
    }

    /**
     * Returns the arrow box classes for the toolbar. The toolbar is
     * always positioned above its related block and has a special class to
     * move its tail on the left.
     *
     * @method getArrowBoxClasses
     * @return {String}
     */
    getArrowBoxClasses() {
        return 'ae-arrow-box ae-arrow-box-bottom';
    }

    /**
     * Sets the position of the toolbar. It overrides the default styles
     * toolbar positioning to position the toolbar just above its related
     * block element. The related block element is the block indicated in
     * CKEditor's path or the target of the editorEvent event.
     *
     * @method setPosition
     * @param {Object} payload
     * @param {AlloyEditor.Core} payload.editor
     * @param {Object} payload.selectionData
     * @param {Object} payload.editorEvent
     * @return {Boolean} true if the method was able to position the
     * toolbar
     */
    setPosition(payload) {
        const domElement = new CKEDITOR.dom.element(ReactDOM.findDOMNode(this));
        const region = payload.selectionData.region;
        const xy = this.getWidgetXYPoint(region.left, region.top, CKEDITOR.SELECTION_BOTTOM_TO_TOP);

        domElement.addClass('ae-toolbar-transition');
        domElement.setStyles({ left: xy[0] + 'px', top: xy[1] + 'px' });

        return true;
    }
}

eZ.addConfig('ezAlloyEditor.ezLinkConfig', EzLinkConfig);
