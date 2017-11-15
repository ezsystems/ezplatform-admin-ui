(function (global) {
    if (CKEDITOR.plugins.get('ezremoveblock')) {
        return;
    }

    const removeBlockCommand = {
        /**
         * Moves the caret to the element
         *
         * @method moveCaretToElement
         * @param {CKEDITOR.editor} editor
         * @param {CKEDITOR.dom.element} element
         */
        moveCaretToElement: function (editor, element) {
            const caretElement = editor.eZ.findCaretElement(element);

            editor.eZ.moveCaretToElement(editor, caretElement);
            this.fireEditorInteraction(editor, caretElement);
        },

        /**
         * Fires the editorInteraction event so that AlloyEditor's UI is updated
         * for the newly focused element
         *
         * @method fireEditorInteraction
         * @param {CKEDITOR.editor} editor
         * @param {CKEDITOR.dom.element} removedElement
         * @param {CKEDITOR.dom.element} newFocus
         */
        fireEditorInteraction: function (editor, newFocus) {
            const event = {
                editor: editor,
                target: newFocus.$,
                name: 'eZRemoveBlockDone',
            };

            editor.fire('editorInteraction', {
                nativeEvent: event,
                selectionData: editor.getSelectionData(),
            });
        },

        /**
         * Changes the focused element in the editor to the given newFocus
         * element
         *
         * @param {CKEDITOR.editor} editor
         * @param {CKEDITOR.dom.element} newFocus
         * @protected
         * @method changeFocus
         */
        changeFocus: function (editor, newFocus) {
            const widget = editor.widgets.getByElement(newFocus);

            if (widget) {
                widget.focus();
            } else {
                this.moveCaretToElement(editor, newFocus);
            }
       },

        exec: function (editor, data) {
            let toRemove = editor.elementPath().block;
            let newFocus;

            if (!toRemove) {
                // path.block is null when a widget is focused so the element to
                // remove is the focused widget wrapper.
                toRemove = editor.widgets.focused.wrapper;
            }

            newFocus = toRemove.getNext();

            if (!newFocus || newFocus.type === CKEDITOR.NODE_TEXT || newFocus.hasAttribute('data-cke-temp')) {
                // the data-cke-temp element is added by the Widget plugin for
                // internal purposes but it exposes no API to handle it, so we
                // are forced to manually check if newFocus is this element
                // see https://jira.ez.no/browse/EZP-26016
                newFocus = toRemove.getPrevious();
            }

            toRemove.remove();

            if (newFocus) {
                this.changeFocus(editor, newFocus);
            }
        },
    };

    /**
     * CKEditor plugin providing the eZRemoveBlock command. This command
     * allows to remove the block element holding the caret or the focused
     * widget
     *
     * @class ezremoveblock
     * @namespace CKEDITOR.plugins
     * @constructor
     */
    CKEDITOR.plugins.add('ezremoveblock', {
        requires: ['widget', 'ezcaret'],

        init: (editor) => editor.addCommand('eZRemoveBlock', removeBlockCommand),
    });
})(window);

