import ReactDOM from 'react-dom';

export default class EzConfigBase {
    static outlineTotalWidth(block) {
        let outlineOffset = parseInt(block.getComputedStyle('outline-offset'), 10);
        const outlineWidth = parseInt(block.getComputedStyle('outline-width'), 10);

        if (isNaN(outlineOffset)) {
            // Older Edge versions (12-14) did not support outline-offset
            // 1 comes from the stylesheet, see theme/alloyeditor/content.css
            outlineOffset = 1;
        }
        return outlineOffset + outlineWidth;
    }

    static isEmpty(block) {
        const node = block.$;
        let count = node.childNodes.length;
        let child = node.firstChild;

        // Special case 1: Safari/Firefox/? puts a <br> (and somtimes also empty text) when you empty <pre>
        if ((count === 1 || count === 2) && node.localName === 'pre' && child.nodeType === 1 && child.localName === 'br') {
            node.removeChild(child);
            if (count === 2) {
                child = node.firstChild;
                count = 1;
            } else {
                node.appendChild(child = document.createTextNode(''));
            }
        }

        if (count === 1) {
            // Special case 2: Safari (12.0) puts 7 ZERO WIDTH SPACE characters when changing to formatted (<pre>)
            if (node.localName === 'pre' && child.nodeType === 3 && child.length === 7 && child.data.replace(/\u200B/g, '').length === 0) {
                child.textContent = '';
            }

            // Considered empty: Tag with <br> (used in <p>) or with empty text node (used among others in <pre>)
            return (child.nodeType === 1 && child.localName === 'br') || (child.nodeType === 3 && child.data === '');
        }

        return count === 0;
    }

    static setPositionFor(block, editor) {
        const blockRect = block.getClientRect();
        const outlineWidth = EzConfigBase.outlineTotalWidth(block);
        const empty = EzConfigBase.isEmpty(block);
        let positionReference = block;
        let left = 0;

        if (editor.widgets.getByElement(block)) {
            left = blockRect.left;
        } else {
            if (empty) {
                block.appendHtml('<span>&nbsp;</span>');
                positionReference = block.findOne('span');
            }

            const range = document.createRange();
            const sl = parseInt(block.$.scrollLeft, 10);
            range.selectNodeContents(positionReference.$);
            left = range.getBoundingClientRect().left + sl;

            if (empty) {
                positionReference.remove();
            }
        }

        const xy = this.getWidgetXYPoint(
            blockRect.left - outlineWidth,
            blockRect.top + block.getWindow().getScrollPosition().y - outlineWidth,
            CKEDITOR.SELECTION_BOTTOM_TO_TOP
        );

        const domElement = new CKEDITOR.dom.element(ReactDOM.findDOMNode(this));
        domElement.addClass('ae-toolbar-transition');
        domElement.setStyles({
            left: (left - outlineWidth) + 'px',
            top: xy[1] + 'px'
        });

        return true;
    }

    getStyles(customStyles = []) {
        const headingLabel = Translator.trans(/*@Desc("Heading")*/ 'toolbar_config_base.heading_label', {}, 'alloy_editor');
        const paragraphLabel = Translator.trans(/*@Desc("Paragraph")*/ 'toolbar_config_base.paragraph_label', {}, 'alloy_editor');
        const formattedLabel = Translator.trans(/*@Desc("Formatted")*/ 'toolbar_config_base.formatted_label', {}, 'alloy_editor');

        return {
            name: 'styles',
            cfg: {
                showRemoveStylesItem: false,
                styles: [
                    {name: `${headingLabel} 1`, style: {element: 'h1'}},
                    {name: `${headingLabel} 2`, style: {element: 'h2'}},
                    {name: `${headingLabel} 3`, style: {element: 'h3'}},
                    {name: `${headingLabel} 4`, style: {element: 'h4'}},
                    {name: `${headingLabel} 5`, style: {element: 'h5'}},
                    {name: `${headingLabel} 6`, style: {element: 'h6'}},
                    {name: paragraphLabel, style: {element: 'p'}},
                    {name: formattedLabel, style: {element: 'pre'}},
                    ...customStyles
                ]
            }
        };
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
        return 'ae-arrow-box ae-arrow-box-bottom ez-ae-arrow-box-left';
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
        const editor = payload.editor.get('nativeEditor');
        let block = editor.elementPath().block;

        if (!block) {
            block = new CKEDITOR.dom.element(payload.editorEvent.data.nativeEvent.target);
        }

        return EzConfigBase.setPositionFor.call(this, block, editor);
    }
}
