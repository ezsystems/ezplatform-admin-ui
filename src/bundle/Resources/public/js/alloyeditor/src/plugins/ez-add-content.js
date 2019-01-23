(function(global) {
    if (global.CKEDITOR.plugins.get('ezaddcontent')) {
        return;
    }

    /**
     * Creates a given HTMLElement
     *
     * @method createElement
     * @return HTMLElement
     */
    const createElement = (doc, tagName, content, attributes) => {
        const element = doc.createElement(tagName);

        element.setAttributes(attributes);
        element.setHtml(content ? content : '<br>');

        return element;
    };

    /**
     * Fires the `editorInteraction` event this is done to make sure the
     * AlloyEditor's UI remains visible
     *
     * @method fireEditorInteractionEvent
     */
    const fireEditorInteractionEvent = (editor, element) => {
        const event = {
            editor: editor,
            target: element.$,
            name: 'eZAddContentDone',
        };

        editor.fire('editorInteraction', {
            nativeEvent: event,
            selectionData: editor.getSelectionData(),
        });
    };

    const isCustomTag = (el) => !!el.findOne('[data-ezelement="eztemplate"]');

    /**
     * Appends the element to the editor content. Depending on the editor's
     * state, the element is added at a different place:
     *
     * - if nothing is selected, editor.insertElement is called and the element
     *   is added at the beginning of the editor
     * - if a block element is selected (not a widget), the element is added
     *   after the element or after the first block in the element path (after
     *   the ul element if a li has the focus)
     * - if a widget has the focus, the element is added right after it
     *
     * @method appendElement
     * @param {CKEDITOR.editor} editor
     * @param {CKEDITOR.dom.element} element
     */
    const appendElement = (editor, element) => {
        const elementPath = editor.elementPath();

        if (elementPath && elementPath.block) {
            const elements = elementPath.elements;
            const insertIndex = !elementPath.contains(isCustomTag, true) ? elements.length - 2 : 0;

            element.insertAfter(elements[insertIndex]);
        } else if (editor.widgets && editor.widgets.focused) {
            element.insertAfter(editor.widgets.focused.wrapper);
        } else {
            editor.insertElement(element);
        }
    };

    const addContentCommand = {
        exec: function(editor, data) {
            const element = createElement(editor.document, data.tagName, data.content, data.attributes);
            let focusElement = element;

            appendElement(editor, focusElement);

            if (data.focusElement) {
                focusElement = element.findOne(data.focusElement);
            }

            editor.eZ.moveCaretToElement(editor, focusElement);
            fireEditorInteractionEvent(editor, focusElement);
        },
    };

    /**
     * CKEditor plugin providing the eZAddContent command. This command
     * allows to add content  to the editor content in the editable region
     * pointed by the selector available under `eZ.editableRegion` in the
     * configuration.
     *
     * @class ezaddcontent
     * @namespace CKEDITOR.plugins
     * @constructor
     */
    global.CKEDITOR.plugins.add('ezaddcontent', {
        requires: ['ezcaret'],

        init: function(editor) {
            editor.eZ = editor.eZ || {};
            editor.eZ.appendElement = appendElement.bind(editor, editor);
            editor.addCommand('eZAddContent', addContentCommand);
        },
    });
})(window);
