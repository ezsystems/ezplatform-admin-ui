(function(global) {
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
        moveCaretToElement: function(editor, element) {
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
        fireEditorInteraction: function(editor, newFocus) {
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
        changeFocus: function(editor, newFocus) {
            const widget = editor.widgets.getByElement(newFocus);

            if (widget) {
                widget.focus();
            } else {
                this.moveCaretToElement(editor, newFocus);
            }
        },

        getElementToRemove(editor) {
            const path = editor.elementPath();
            let toRemove = editor.widgets.focused ? editor.widgets.focused.wrapper : path.block;

            if (!toRemove) {
                toRemove = path.elements.find((element) => element.$.dataset.ezelement === 'eztemplateinline');
            }

            return toRemove.is('li') ? toRemove.getParent() : toRemove;
        },

        getElementToFocus(elementToRemove) {
            let elementToFocus = elementToRemove.getNext();

            if (!elementToFocus || elementToFocus.type === CKEDITOR.NODE_TEXT || elementToFocus.hasAttribute('data-cke-temp')) {
                elementToFocus = elementToRemove.getPrevious();
            }

            if (elementToFocus && elementToFocus.type === CKEDITOR.NODE_TEXT) {
                elementToFocus = elementToFocus.getParent();
            }

            if (!elementToFocus) {
                elementToFocus = elementToRemove.getParent();
            }

            return elementToFocus;
        },

        exec: function(editor) {
            const elementToRemove = this.getElementToRemove(editor);
            let elementToFocus = this.getElementToFocus(elementToRemove);

            elementToRemove.remove();

            if (elementToFocus) {
                if (elementToFocus.hasClass('ez-data-source__richtext')) {
                    elementToFocus = new CKEDITOR.dom.element('p');

                    editor.insertElement(elementToFocus);
                }

                this.changeFocus(editor, elementToFocus);
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
