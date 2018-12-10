(function(global) {
    const eZ = (global.eZ = global.eZ || {});

    const BaseRichText = class BaseRichText {
        constructor() {
            this.ezNamespace = 'http://ez.no/namespaces/ezpublish5/xhtml5/edit';
            this.xhtmlNamespace = 'http://www.w3.org/1999/xhtml';
            this.customTags = Object.keys(global.eZ.adminUiConfig.richTextCustomTags);
            this.customTagsToolbars = this.customTags.map((customTag) => {
                const alloyEditorConfig = global.eZ.adminUiConfig.richTextCustomTags[customTag];

                return new global.eZ.ezAlloyEditor.ezCustomTagConfig({
                    name: customTag,
                    alloyEditor: alloyEditorConfig,
                });
            });
            this.customStylesConfigurations = Object.entries(global.eZ.adminUiConfig.richTextCustomStyles).map(
                ([customStyleName, customStyleConfig]) => {
                    return {
                        name: customStyleConfig.label,
                        style: {
                            element: customStyleConfig.inline ? 'span' : 'div',
                            attributes: {
                                'data-ezelement': customStyleConfig.inline ? 'eztemplateinline' : 'eztemplate',
                                'data-eztype': 'style',
                                'data-ezname': customStyleName,
                            },
                        },
                    };
                }
            );

            this.xhtmlify = this.xhtmlify.bind(this);
        }

        getHTMLDocumentFragment(data) {
            const fragment = document.createDocumentFragment();
            const root = fragment.ownerDocument.createElement('div');
            const parsedHTML = new DOMParser().parseFromString(data, 'text/xml');
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

            if (!parsedHTML || !parsedHTML.documentElement || parsedHTML.querySelector('parsererror')) {
                console.warn('Unable to parse the content of the RichText field');

                return fragment;
            }

            fragment.appendChild(root);

            importChildNodes(root, parsedHTML.documentElement, true);
            return fragment;
        }

        emptyEmbed(embedNode) {
            let element = embedNode.firstChild;
            let next;
            let removeClass = function() {};

            while (element) {
                next = element.nextSibling;
                if (!element.getAttribute || !element.getAttribute('data-ezelement')) {
                    embedNode.removeChild(element);
                }
                element = next;
            }

            embedNode.classList.forEach(function(cl) {
                let prevRemoveClass = removeClass;

                if (cl.indexOf('is-embed-') === 0) {
                    removeClass = function() {
                        embedNode.classList.remove(cl);
                        prevRemoveClass();
                    };
                }
            });
            removeClass();
        }

        xhtmlify(data) {
            const doc = document.implementation.createDocument(this.xhtmlNamespace, 'html', null);
            const htmlDoc = document.implementation.createHTMLDocument('');
            const section = htmlDoc.createElement('section');
            let body = htmlDoc.createElement('body');

            section.innerHTML = data;
            body.appendChild(section);
            body = doc.importNode(body, true);
            doc.documentElement.appendChild(body);

            return body.innerHTML;
        }

        init(container) {
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
                            ...this.customTags,
                        ],
                        tabIndex: 2,
                    },
                    styles: {
                        selections: [
                            ...this.customTagsToolbars,
                            new window.eZ.ezAlloyEditor.ezLinkConfig(),
                            new window.eZ.ezAlloyEditor.ezTextConfig({ customStyles: this.customStylesConfigurations }),
                            new window.eZ.ezAlloyEditor.ezParagraphConfig({ customStyles: this.customStylesConfigurations }),
                            new window.eZ.ezAlloyEditor.ezFormattedConfig({ customStyles: this.customStylesConfigurations }),
                            new window.eZ.ezAlloyEditor.ezCustomStyleConfig({ customStyles: this.customStylesConfigurations }),
                            new window.eZ.ezAlloyEditor.ezHeadingConfig({ customStyles: this.customStylesConfigurations }),
                            new window.eZ.ezAlloyEditor.ezTableConfig(),
                            new window.eZ.ezAlloyEditor.ezEmbedImageLinkConfig(),
                            new window.eZ.ezAlloyEditor.ezEmbedImageConfig(),
                            new window.eZ.ezAlloyEditor.ezEmbedConfig(),
                        ],
                        tabIndex: 1,
                    },
                },
                extraPlugins:
                    AlloyEditor.Core.ATTRS.extraPlugins.value +
                    ',' +
                    ['ezaddcontent', 'ezmoveelement', 'ezremoveblock', 'ezembed', 'ezfocusblock', 'ezcustomtag', 'ezcountchars'].join(','),
            });

            const wrapper = this.getHTMLDocumentFragment(container.closest('.ez-data-source').querySelector('textarea').value);
            const section = wrapper.childNodes[0];

            if (!section.hasChildNodes()) {
                section.appendChild(document.createElement('p'));
            }

            alloyEditor.get('nativeEditor').setData(section.innerHTML);

            container.addEventListener('blur', (event) => {
                const data = alloyEditor.get('nativeEditor').getData();
                const doc = document.createDocumentFragment();
                const root = document.createElement('div');

                root.innerHTML = data;
                doc.appendChild(root);

                [...doc.querySelectorAll('[data-ezelement="ezembed"]')].forEach(this.emptyEmbed);
                [...doc.querySelectorAll('[data-ezelement="ezcustomtag"]')].forEach(this.emptyEmbed);

                event.target.closest('.ez-data-source').querySelector('textarea').value = this.xhtmlify(root.innerHTML).replace(
                    this.xhtmlNamespace,
                    this.ezNamespace
                );
            });

            return alloyEditor;
        }
    };

    eZ.BaseRichText = BaseRichText;
})(window);
