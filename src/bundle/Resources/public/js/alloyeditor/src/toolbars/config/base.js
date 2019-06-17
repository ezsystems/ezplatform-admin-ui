import ReactDOM from 'react-dom';

export default class EzConfigBase {
    static outlineTotalWidth(block) {
        let outlineOffset = parseInt(block.getComputedStyle('outline-offset'), 10);
        const outlineWidth = parseInt(block.getComputedStyle('outline-width'), 10);

        if (isNaN(outlineOffset)) {
            // Edge does not support offset-offset yet
            // 1 comes from the stylesheet, see theme/alloyeditor/content.css
            outlineOffset = 1;
        }
        return outlineOffset + outlineWidth;
    }

    static isEmpty(block) {
        const nodes = [...block.$.childNodes];
        const count = nodes.length;
        const areAllTextNodesEmpty = !!count && nodes.every((node) => node.nodeName === '#text' && !node.data.replace(/\u200B/g, ''));
        const isOnlyBreakLine = count === 1 && block.$.childNodes.item(0).localName === 'br';

        return count === 0 || isOnlyBreakLine || areAllTextNodesEmpty;
    }

    static setPositionFor(block, editor, getTopPosition) {
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
            const scrollLeft = parseInt(block.$.scrollLeft, 10);
            range.selectNodeContents(positionReference.$);
            left = range.getBoundingClientRect().left + scrollLeft;

            if (empty) {
                positionReference.remove();
            }
        }

        const topPosition = getTopPosition(block, editor);

        const domElement = new CKEDITOR.dom.element(ReactDOM.findDOMNode(this));

        domElement.addClass('ae-toolbar-transition');
        domElement.setStyles({
            left: left - outlineWidth + 'px',
            top: topPosition + 'px',
        });

        return true;
    }

    static getTopPosition(block, editor) {
        const blockRect = block.getClientRect();
        const outlineWidth = EzConfigBase.outlineTotalWidth(block);
        const xy = this.getWidgetXYPoint(
            blockRect.left - outlineWidth,
            blockRect.top + block.getWindow().getScrollPosition().y - outlineWidth,
            CKEDITOR.SELECTION_BOTTOM_TO_TOP
        );

        return xy[1];
    }

    static getBlockElement(payload) {
        const editor = payload.editor.get('nativeEditor');
        const nativeEvent = payload.editorEvent.data.nativeEvent;
        const targetElement = nativeEvent ? new CKEDITOR.dom.element(payload.editorEvent.data.nativeEvent.target) : null;
        const isWidgetElement = targetElement ? editor.widgets.getByElement(targetElement) : false;
        const path = editor.elementPath();
        let block = path.block;

        if (!block || isWidgetElement) {
            const inlineCustomTag = path.elements.find((element) => element.$.dataset.ezelement === 'eztemplateinline');

            block = inlineCustomTag || targetElement;
        }

        if (block.is('li')) {
            block = block.getParent();
        }

        return block;
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
                    { name: `${headingLabel} 1`, style: { element: 'h1' } },
                    { name: `${headingLabel} 2`, style: { element: 'h2' } },
                    { name: `${headingLabel} 3`, style: { element: 'h3' } },
                    { name: `${headingLabel} 4`, style: { element: 'h4' } },
                    { name: `${headingLabel} 5`, style: { element: 'h5' } },
                    { name: `${headingLabel} 6`, style: { element: 'h6' } },
                    { name: paragraphLabel, style: { element: 'p' } },
                    { name: formattedLabel, style: { element: 'pre' } },
                    ...customStyles,
                ],
            },
        };
    }

    getEditAttributesButton(config) {
        return config.attributes[this.name] || config.classes[this.name] ? `${this.name}edit` : '';
    }

    addExtraButtons(extraButtons = {}) {
        if (extraButtons[this.name]) {
            this.buttons = [...this.buttons, ...extraButtons[this.name]];
        }
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
        const block = EzConfigBase.getBlockElement(payload);

        return EzConfigBase.setPositionFor.call(this, block, editor, EzConfigBase.getTopPosition.bind(this));
    }
}
