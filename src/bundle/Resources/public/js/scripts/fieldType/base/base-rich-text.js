(function(global) {
    const eZ = (global.eZ = global.eZ || {});
    const TEXT_NODE = 3;

    const BaseRichText = class BaseRichText {
        constructor() {
            this.ezNamespace = 'http://ez.no/namespaces/ezpublish5/xhtml5/edit';
            this.xhtmlNamespace = 'http://www.w3.org/1999/xhtml';
            this.customTags = Object.keys(global.eZ.adminUiConfig.richTextCustomTags).filter(
                (key) => !global.eZ.adminUiConfig.richTextCustomTags[key].isInline
            );
            this.inlineCustomTags = Object.keys(global.eZ.adminUiConfig.richTextCustomTags).filter(
                (key) => global.eZ.adminUiConfig.richTextCustomTags[key].isInline
            );
            this.alloyEditorExtraButtons = {
                'ezadd': [],
                'link': [],
                'text': [],
                'table': [],
                ...global.eZ.adminUiConfig.alloyEditor.extraButtons
            };
            this.customTagsToolbars = this.customTags.map((customTag) => {
                const alloyEditorConfig = global.eZ.adminUiConfig.richTextCustomTags[customTag];

                return new global.eZ.ezAlloyEditor.ezCustomTagConfig({
                    name: customTag,
                    alloyEditor: alloyEditorConfig,
                    extraButtons: this.alloyEditorExtraButtons,
                });
            });
            this.inlineCustomTagsToolbars = this.inlineCustomTags.map((customTag) => {
                const alloyEditorConfig = global.eZ.adminUiConfig.richTextCustomTags[customTag];

                return new global.eZ.ezAlloyEditor.ezInlineCustomTagConfig({
                    name: customTag,
                    alloyEditor: alloyEditorConfig,
                    extraButtons: this.alloyEditorExtraButtons,
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
            this.alloyEditorExtraPlugins = global.eZ.adminUiConfig.alloyEditor.extraPlugins;

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

        clearCustomTag(customTag) {
            const attributesNodes = [...customTag.querySelectorAll('[data-ezelement="ezattributes"]')];
            const headers = [...customTag.querySelectorAll('.ez-custom-tag__header')];

            attributesNodes.forEach((attributesNode) => attributesNode.remove());
            headers.forEach((header) => header.remove());
        }

        clearAnchor(element) {
            const icon = element.querySelector('.ez-icon--anchor');

            if (icon) {
                icon.remove();
            } else {
                element.classList.remove('ez-has-anchor');
            }
        }

        appendAnchorIcon(element) {
            const container = document.createElement('div');
            const icon = `
                <svg class="ez-icon ez-icon--small ez-icon--secondary ez-icon--anchor">
                    <use xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#link-anchor"></use>
                </svg>`;

            container.insertAdjacentHTML('afterbegin', icon);

            const svg = new CKEDITOR.dom.element(container.querySelector('svg'));
            const ckeditorElement = new CKEDITOR.dom.element(element);

            ckeditorElement.append(svg, true);
        }

        clearInlineCustomTag(inlineCustomTag) {
            const icons = [...inlineCustomTag.querySelectorAll('.ez-custom-tag__icon-wrapper')];

            icons.forEach((icon) => icon.remove());
        }

        init(container) {
            const toolbarProps = {extraButtons: this.alloyEditorExtraButtons};
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
                            ...this.alloyEditorExtraButtons['ezadd'],
                        ],
                        tabIndex: 2,
                    },
                    styles: {
                        selections: [
                            ...this.customTagsToolbars,
                            new window.eZ.ezAlloyEditor.ezLinkConfig(toolbarProps),
                            new window.eZ.ezAlloyEditor.ezTextConfig({
                                customStyles: this.customStylesConfigurations,
                                inlineCustomTags: this.inlineCustomTags,
                                ...toolbarProps
                            }),
                            ...this.inlineCustomTagsToolbars,
                            new window.eZ.ezAlloyEditor.ezParagraphConfig({ customStyles: this.customStylesConfigurations, ...toolbarProps }),
                            new window.eZ.ezAlloyEditor.ezFormattedConfig({ customStyles: this.customStylesConfigurations, ...toolbarProps }),
                            new window.eZ.ezAlloyEditor.ezCustomStyleConfig({ customStyles: this.customStylesConfigurations, ...toolbarProps }),
                            new window.eZ.ezAlloyEditor.ezHeadingConfig({ customStyles: this.customStylesConfigurations, ...toolbarProps }),
                            new window.eZ.ezAlloyEditor.ezListConfig({ customStyles: this.customStylesConfigurations, ...toolbarProps }),
                            new window.eZ.ezAlloyEditor.ezEmbedInlineConfig(toolbarProps),
                            new window.eZ.ezAlloyEditor.ezTableConfig(toolbarProps),
                            new window.eZ.ezAlloyEditor.ezEmbedImageLinkConfig(toolbarProps),
                            new window.eZ.ezAlloyEditor.ezEmbedImageConfig(toolbarProps),
                            new window.eZ.ezAlloyEditor.ezEmbedConfig(toolbarProps),
                        ],
                        tabIndex: 1,
                    },
                },
                extraPlugins:
                    AlloyEditor.Core.ATTRS.extraPlugins.value +
                    ',' +
                    [
                        'ezaddcontent',
                        'ezmoveelement',
                        'ezremoveblock',
                        'ezembed',
                        'ezembedinline',
                        'ezfocusblock',
                        'ezcustomtag',
                        'ezinlinecustomtag',
                        ...this.alloyEditorExtraPlugins
                    ].join(','),
            });
            const wrapper = this.getHTMLDocumentFragment(container.closest('.ez-data-source').querySelector('textarea').value);
            const section = wrapper.childNodes[0];
            const nativeEditor = alloyEditor.get('nativeEditor');
            const saveRichText = () => {
                const data = alloyEditor.get('nativeEditor').getData();
                const doc = document.createDocumentFragment();
                const root = document.createElement('div');

                root.innerHTML = data;
                doc.appendChild(root);

                [
                    ...doc.querySelectorAll('[data-ezelement="ezembed"]'),
                    ...doc.querySelectorAll('[data-ezelement="ezembedinline"]'),
                ].forEach(this.emptyEmbed);
                [...doc.querySelectorAll('[data-ezelement="eztemplate"]:not([data-eztype="style"])')].forEach(this.clearCustomTag);
                [...doc.querySelectorAll('.ez-has-anchor')].forEach(this.clearAnchor);
                [...doc.querySelectorAll('[data-ezelement="eztemplateinline"]:not([data-eztype="style"])')].forEach(
                    this.clearInlineCustomTag
                );

                container.closest('.ez-data-source').querySelector('textarea').value = this.xhtmlify(root.innerHTML).replace(
                    this.xhtmlNamespace,
                    this.ezNamespace
                );

                this.countWordsCharacters(container, doc);
            };

            if (!section.hasChildNodes()) {
                section.appendChild(document.createElement('p'));
            }

            nativeEditor.once('dataReady', () => container.querySelectorAll('.ez-has-anchor').forEach(this.appendAnchorIcon));

            this.countWordsCharacters(container, section);
            nativeEditor.setData(section.innerHTML);

            nativeEditor.on('blur', saveRichText);
            nativeEditor.on('change', saveRichText);
            nativeEditor.on('customUpdate', saveRichText);
            nativeEditor.on('editorInteraction', saveRichText);

            return alloyEditor;
        }

        countWordsCharacters(container, editorHtml) {
            const counterWrapper = container.parentElement.querySelector('.ez-character-counter');

            if (counterWrapper) {
                const wordWrapper = counterWrapper.querySelector('.ez-character-counter__word-count');
                const charactersWrapper = counterWrapper.querySelector('.ez-character-counter__character-count');
                const words = this.getTextNodeValues(editorHtml);

                wordWrapper.innerText = words.length;
                charactersWrapper.innerText = words.join(' ').length;
            }
        }

        getTextNodeValues(node) {
            let values = [];

            const pushValue = (node) => {
                if (node.nodeType === TEXT_NODE) {
                    const nodeValue = this.sanitize(node.nodeValue);

                    values = values.concat(this.splitIntoWords(nodeValue));
                }
            };

            this.iterateThroughChildNodes(node, pushValue);

            return values;
        }

        iterateThroughChildNodes(node, callback) {
            if (typeof node.getAttribute === 'function' && node.getAttribute('data-ezelement') === 'ezconfig') {
                return;
            }
            callback(node);
            node = node.firstChild;

            while (node) {
                this.iterateThroughChildNodes(node, callback);
                node = node.nextSibling;
            }
        }

        sanitize(text) {
            return text.replace(/[\u200B-\u200D\uFEFF]/g, '');
        }

        splitIntoWords(text) {
            return text.split(' ').filter((word) => word.trim());
        }
    };

    eZ.BaseRichText = BaseRichText;
})(window);
