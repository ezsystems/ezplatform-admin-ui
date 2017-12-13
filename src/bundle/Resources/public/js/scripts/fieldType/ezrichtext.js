(function (global, doc) {
    const SELECTOR_FIELD = '.ez-field-edit--ezrichtext';
    const SELECTOR_INPUT = '.ez-data-source__richtext';

    class EzRichTextValidator extends global.eZ.BaseFieldValidator {
        constructor(config) {
            super(config);

            this.alloyEditor = config.alloyEditor;
        }
        /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzRichTextValidator
         */
        validateInput(event) {
            const fieldContainer = event.currentTarget.closest(SELECTOR_FIELD);
            const isRequired = fieldContainer.classList.contains('ez-field-edit--required');
            const label = fieldContainer.querySelector('.ez-field-edit__label').innerHTML;
            const isEmpty = !this.alloyEditor.get('nativeEditor').getData().length;
            const isError = isRequired && isEmpty;
            const result = { isError };

            if (isError) {
                result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);
            }

            return result;
        }
    }

    [...doc.querySelectorAll(`${SELECTOR_FIELD} ${SELECTOR_INPUT}`)].forEach(container => {
        const alloyEditor = global.AlloyEditor.editable(container.getAttribute('id'), {
            toolbars: {
                ezadd: {
                    buttons: [
                        'ezheading',
                        'ezparagraph',
                        'ezunorderedlist',
                        'ezorderedlist',
                        'ezimage',
                        'ezembed',
                        'eztable',
                    ],
                    tabIndex: 2,
                },
                styles: {
                    selections: [
                        new window.eZ.ezAlloyEditor.ezLinkConfig(),
                        new window.eZ.ezAlloyEditor.ezTextConfig(),
                        new window.eZ.ezAlloyEditor.ezParagraphConfig(),
                        new window.eZ.ezAlloyEditor.ezHeadingConfig(),
                        new window.eZ.ezAlloyEditor.ezTableConfig(),
                        new window.eZ.ezAlloyEditor.ezEmbedImageConfig(),
                        new window.eZ.ezAlloyEditor.ezEmbedConfig(),
                    ],
                    tabIndex: 1
                },
            },
            extraPlugins: AlloyEditor.Core.ATTRS.extraPlugins.value + ',' + [
                'ezaddcontent',
                'ezmoveelement',
                'ezremoveblock',
                'ezembed',
                'ezfocusblock',
            ].join(','),
        });
        const getHTMLDocumentFragment = function (data) {
            const fragment = document.createDocumentFragment();
            const root = fragment.ownerDocument.createElement('div');
            const doc = (new DOMParser()).parseFromString(data, 'text/xml');
            const importChildNodes = (parent, element, skipElement) => {
                let i;
                let newElement;

                if (skipElement) {
                    newElement = parent;
                } else {
                    if (element.nodeType === Node.ELEMENT_NODE) {
                        newElement = parent.ownerDocument.createElement(element.localName);
                        for (i = 0; i !== element.attributes.length; i++) {
                            importChildNodes(newElement, element.attributes[i], false);
                        }
                        parent.appendChild(newElement);
                    } else if (element.nodeType === Node.TEXT_NODE) {
                        parent.appendChild(parent.ownerDocument.createTextNode(element.nodeValue));
                        return;
                    } else if (element.nodeType === Node.ATTRIBUTE_NODE) {
                        parent.setAttribute(element.localName, element.value);
                        return;
                    } else {
                        return;
                    }
                }

                for (i = 0; i !== element.childNodes.length; i++) {
                    importChildNodes(newElement, element.childNodes[i], false);
                }
            };

            if (!doc || !doc.documentElement || doc.querySelector('parsererror')) {
                console.warn('Unable to parse the content of the RichText field');

                return fragment;
            }

            fragment.appendChild(root);

            importChildNodes(root, doc.documentElement, true);
            return fragment;
        };
        const wrapper = getHTMLDocumentFragment(container.closest('.ez-data-source').querySelector('textarea').value);
        const section = wrapper.childNodes[0];

        if (!section.hasChildNodes()) {
            section.appendChild(document.createElement('p'));
        }

        alloyEditor.get('nativeEditor').setData(section.innerHTML);

        container.addEventListener('blur', event => {
            const data = alloyEditor.get('nativeEditor').getData();
            const doc = document.createDocumentFragment();
            const root = document.createElement('div');
            const ezNamespace = 'http://ez.no/namespaces/ezpublish5/xhtml5/edit';
            const xhtmlNamespace = 'http://www.w3.org/1999/xhtml';
            const emptyEmbed = function (embedNode) {
                let element = embedNode.firstChild;
                let next;
                let removeClass = function () {};

                while (element) {
                    next = element.nextSibling;
                    if (!element.getAttribute || !element.getAttribute('data-ezelement')) {
                        embedNode.removeChild(element);
                    }
                    element = next;
                }

                embedNode.classList.forEach(function (cl) {
                    let prevRemoveClass = removeClass;

                    if (cl.indexOf('is-embed-') === 0) {
                        removeClass = function () {
                            embedNode.classList.remove(cl);
                            prevRemoveClass();
                        };
                    }
                });
                removeClass();
            };
            const xhtmlify = function (data) {
                const doc = document.implementation.createDocument(xhtmlNamespace, 'html', null);
                const htmlDoc =  document.implementation.createHTMLDocument("");
                const section = htmlDoc.createElement('section');
                let body = htmlDoc.createElement('body');

                section.innerHTML = data;
                body.appendChild(section);
                body = doc.importNode(body, true);
                doc.documentElement.appendChild(body);

                return body.innerHTML;
            };

            root.innerHTML = xhtmlify(data).replace(xhtmlNamespace, ezNamespace);
            doc.appendChild(root);

            [...doc.querySelectorAll('[data-ezelement="ezembed"]')].forEach(emptyEmbed);

            event.target.closest('.ez-data-source').querySelector('textarea').value = root.innerHTML;
        });

        const validator = new EzRichTextValidator({
            classInvalid: 'is-invalid',
            fieldContainer: container.closest(SELECTOR_FIELD),
            alloyEditor,
            eventsMap: [
                {
                    selector: SELECTOR_INPUT,
                    eventName: 'input',
                    callback: 'validateInput',
                    errorNodeSelectors: ['.ez-field-edit__label-wrapper'],
                },
                {
                    selector: SELECTOR_INPUT,
                    eventName: 'blur',
                    callback: 'validateInput',
                    errorNodeSelectors: ['.ez-field-edit__label-wrapper'],
                },
            ],
        });

        validator.init();

        global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
            [...global.eZ.fieldTypeValidators, validator] :
            [validator];
    });
})(window, document);
