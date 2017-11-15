(function (global) {
    if (CKEDITOR.plugins.get('ezmoveelement')) {
        return;
    }

    /**
     * Fires the editorInteraction event.
     *
     * @method fireEditorInteraction
     * @param {Object} editor the CKEditor
     * @param {Event} evt the event object
     * @param {Object} target the target
     */
    const fireEditorInteraction = (editor, evt, target) => {
        const event = {
            editor: editor,
            target: target.$,
            name: evt,
        };

        editor.fire('editorInteraction', {
            nativeEvent: event,
            selectionData: editor.getSelectionData(),
        });
    };

    const moveUpCommand = {
        exec: function (editor, data) {
            let focused = editor.elementPath().block;
            let widget;

            if (!focused) {
                widget = editor.widgets.focused;
                focused = widget.wrapper;
            }

            const previous = focused.getPrevious();

            if (previous) {
                if (widget) {
                    widget.moveBefore(previous);
                } else {
                    focused.insertBefore(previous);
                    editor.eZ.moveCaretToElement(editor, editor.eZ.findCaretElement(focused));
                    fireEditorInteraction(editor, 'eZMoveUpDone', focused);
                }
            }
        },
    };

    const moveDownCommand = {
        exec: function (editor, data) {
            let focused = editor.elementPath().block;
            let widget;

            if (!focused) {
                widget = editor.widgets.focused;
                focused = widget.wrapper;
            }

            const next = focused.getNext();

            if (next) {
                if (widget) {
                    widget.moveAfter(next);
                } else {
                    focused.insertAfter(next);
                    editor.eZ.moveCaretToElement(editor, editor.eZ.findCaretElement(focused));
                    fireEditorInteraction(editor, 'eZMoveDownDone', focused);
                }
            }
        },
    };

    /**
     * CKEditor plugin providing the eZMoveUp and eZMoveDown commands. These
     * commands allow to move the element having the focus in the editor.
     *
     * @class ezmoveelement
     * @namespace CKEDITOR.plugins
     * @constructor
     */
    CKEDITOR.plugins.add('ezmoveelement', {
        requires: ['ezcaret'],

        init: function (editor) {
            editor.addCommand('eZMoveUp', moveUpCommand);
            editor.addCommand('eZMoveDown', moveDownCommand);
        },
    });
})(window);
