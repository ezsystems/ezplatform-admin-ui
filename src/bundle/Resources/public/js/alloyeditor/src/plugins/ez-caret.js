(function (global) {
    if (CKEDITOR.plugins.get('ezcaret')) {
        return;
    }

    /**
     * Moves caret to the element.
     *
     * @method moveCaretToElement
     */
    const moveCaretToElement = (editor, element) => {
        const range = editor.createRange();

        range.moveToPosition(element, CKEDITOR.POSITION_AFTER_START);
        editor.getSelection().selectRanges([range]);
    }

    /**
     * Finds caret element.
     *
     * @method findCaretElement
     * @return HTMLElement
     */
    const findCaretElement = (element) => {
        const child = element.getChild(0);

        if (child && child.type !== CKEDITOR.NODE_TEXT) {
            return findCaretElement(child);
        }

        return element;
    }

    /**
     * CKEDITOR plugin providing an API to handle the caret in the editor.
     *
     * @class ezcaret
     * @namespace CKEDITOR.plugins
     * @constructor
     */
    CKEDITOR.plugins.add('ezcaret', {
        init: function (editor) {
            editor.eZ = editor.eZ || {};

            /**
             * Moves the caret in the editor to the given element
             *
             * @method eZ.moveCaretToElement
             * @param {CKEDITOR.editor} editor
             * @param {CKEDITOR.dom.element} element
             */
            editor.eZ.moveCaretToElement = moveCaretToElement;

            /**
             * Finds the "caret element" for the given element. For some elements,
             * like ul or table, moving the caret inside them actually means finding
             * the first element that can be filled by the user.
             *
             * @method eZ.findCaretElement
             * @protected
             * @param {CKEDITOR.dom.element} element
             * @return {CKEDITOR.dom.element}
             */
            editor.eZ.findCaretElement = findCaretElement;
        },
    });
})(window);

