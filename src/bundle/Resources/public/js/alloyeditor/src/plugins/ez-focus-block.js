(function(global) {
    const FOCUSED_CLASS = 'is-block-focused';

    if (CKEDITOR.plugins.get('ezfocusblock')) {
        return;
    }

    /**
     * Finds the focused blocks.
     *
     * @method findFocusedBlock
     * @param {Object} editor the CKEditor
     */
    const findFocusedBlock = (editor) => editor.element.findOne('.' + FOCUSED_CLASS);

    /**
     * Finds blocks to focus.
     *
     * @method findNewFocusedBlock
     * @param {Object} elementPath the element path
     */
    const findNewFocusedBlock = (elementPath) => {
        const block = elementPath.block;
        const elements = elementPath.elements;

        if (!block) {
            return null;
        }

        return elements[elements.length - 2];
    };

    /**
     * Updates the focused blocks.
     *
     * @method updateFocusedBlock
     * @param {Event} event the event object
     */
    const updateFocusedBlock = (event) => {
        const block = findNewFocusedBlock(event.data.path);
        const oldBlock = findFocusedBlock(event.editor);

        if (oldBlock && (!block || block.$ !== oldBlock.$)) {
            oldBlock.removeClass(FOCUSED_CLASS);
        }

        if (block) {
            block.addClass(FOCUSED_CLASS);
        }
    };

    /**
     * Clear the focus from block.
     *
     * @method clearFocusedBlock
     * @param {Event} event the event object
     */
    const clearFocusedBlock = (event) => {
        const oldBlock = findFocusedBlock(event.editor);

        if (oldBlock) {
            oldBlock.removeClass(FOCUSED_CLASS);
        }
    };

    /**
     * Clear the focus blocks from data.
     *
     * @method clearFocusedBlockFromData
     * @param {Event} event the event object
     */
    const clearFocusedBlockFromData = (event) => {
        const doc = document.createDocumentFragment();
        const root = document.createElement('div');
        let i;

        doc.appendChild(root);
        root.innerHTML = event.data.dataValue;
        const list = root.querySelectorAll('.' + FOCUSED_CLASS);

        if (list.length) {
            for (i = 0; i != list.length; ++i) {
                const element = list[i];

                element.classList.remove(FOCUSED_CLASS);

                if (!element.getAttribute('class')) {
                    element.removeAttribute('class');
                }
            }
            event.data.dataValue = root.innerHTML;
        }
    };

    /**
     * CKEditor plugin to add/remove the focused class on the block holding the
     * caret.
     *
     * @class ezfocusblock
     * @namespace CKEDITOR.plugins
     * @constructor
     */
    CKEDITOR.plugins.add('ezfocusblock', {
        init: function(editor) {
            editor.on('selectionChange', updateFocusedBlock);
            editor.on('blur', clearFocusedBlock);
            editor.on('getData', clearFocusedBlockFromData);
        },
    });
})(window);
