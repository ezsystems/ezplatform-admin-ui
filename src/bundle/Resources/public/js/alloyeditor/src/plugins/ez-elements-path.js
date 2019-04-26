(function(global) {
    if (CKEDITOR.plugins.get('ezelementspath')) {
        return;
    }

    const elementsLabel = {
        ezcustomtag: 'Custom Tag',
        ezinlinecustomtag: 'Inline Custom Tag',
        ezembed: 'Embed',
        ezembedinline: 'Embed Inline',
        ezembedimage: 'Embed Image',
    };
    const skipElementsSelectors = ['.cke_widget_element', '.cke_widget_editable'];
    const getLabel = (elementIdentifier) => (elementsLabel[elementIdentifier] ? elementsLabel[elementIdentifier] : elementIdentifier);
    const itemPathTemplate = new CKEDITOR.template('<li class="ez-elements-path__item">{label}</li>');
    const updatePath = (event) => {
        const { editor, data } = event;
        const elements = [...data.path.elements]
            .reverse()
            .slice(1)
            .map(removeSkippedElements)
            .filter((element) => !!element);
        const pathItems = elements.map(createPathItem.bind(this, editor));
        const pathContainer = editor.container.getParent().findOne('.ez-elements-path');

        if (!pathContainer) {
            return;
        }

        pathContainer.setHtml('');
        pathItems.forEach((pathItem) => pathContainer.append(pathItem));
    };
    const removeSkippedElements = (element) => {
        const container = new CKEDITOR.dom.element('div');
        const containerWrapper = new CKEDITOR.dom.element('div');
        const clonedElement = element.clone(true);

        container.addClass('ez-cloned');
        container.append(clonedElement);
        containerWrapper.append(container);

        const shouldBeSkipped = skipElementsSelectors.some((selector) => containerWrapper.findOne(`.ez-cloned > ${selector}`));

        return shouldBeSkipped ? null : element;
    };
    const createPathItem = (editor, element) => {
        const label = getElementLabel(editor, element);
        const pathItem = CKEDITOR.dom.element.createFromHtml(itemPathTemplate.output({ label }));

        pathItem.on('click', selectElement.bind(this, editor, element));

        return pathItem;
    };
    const selectElement = (editor, element) => {
        const selection = editor.getSelection();

        selection.selectElement(element);

        if (isWidgetElement(editor, element)) {
            return;
        }

        editor.fire('editorInteraction', {
            nativeEvent: {
                editor: editor,
                target: element.$,
            },
            selectionData: editor.getSelectionData(),
        });
    };
    const getElementLabel = (editor, element) => {
        const widgetIdentifier = getWidgetIdentifier(editor, element);
        const label = widgetIdentifier ? getLabel(widgetIdentifier) : getLabel(element.getName());

        return label;
    };
    const getWidgetIdentifier = (editor, element) => {
        const widget = editor.widgets.getByElement(element);
        const widgetIdentifier =
            isWidgetElement(editor, element) && typeof widget.getIdentifier === 'function' ? widget.getIdentifier() : null;

        return widgetIdentifier;
    };
    const isWidgetElement = (editor, element) => {
        const widget = editor.widgets.getByElement(element);
        const elementFirstChild = element.getFirst();

        return widget && elementFirstChild.type === 1 && widget.element.isIdentical(elementFirstChild);
    };

    CKEDITOR.plugins.add('ezelementspath', {
        init: function(editor) {
            editor.on('selectionChange', updatePath);
        },
    });
})(window);
