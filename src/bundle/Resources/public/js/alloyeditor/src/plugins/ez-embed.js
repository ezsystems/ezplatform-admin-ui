import embedBaseDefinition from '../widgets/ez-embed-base';

(function(global) {
    if (CKEDITOR.plugins.get('ezembed') && CKEDITOR.plugins.get('ezembedinline')) {
        return;
    }

    /**
     * CKEditor plugin to configure the widget plugin so that it recognizes the
     * `div[data-ezelement="embed"]` elements as widget.
     *
     * @class ezembed
     * @namespace CKEDITOR.plugins
     * @constructor
     */
    CKEDITOR.plugins.add('ezembed', {
        requires: 'widget,ezaddcontent',

        init: function(editor) {
            editor.ezembed = {
                canBeAdded: () => {
                    const path = editor.elementPath();

                    return !path || path.contains('table', true) === null;
                },
            };

            const embedDefinition = Object.assign({}, embedBaseDefinition, { editor });

            editor.widgets.add('ezembed', embedDefinition);
        },
    });

    /**
     * CKEditor plugin to configure the widget plugin so that it recognizes the
     * `div[data-ezelement="embedinline"]` elements as widget.
     *
     * @class ezembedinline
     * @namespace CKEDITOR.plugins
     * @constructor
     */
    CKEDITOR.plugins.add('ezembedinline', {
        requires: 'widget,ezaddcontent',

        init: function(editor) {
            const embedInlineDefinition = Object.assign({}, embedBaseDefinition, {
                editor,
                defaults: {
                    href: 'ezcontent://',
                    content: 'home',
                    view: 'embed-inline',
                },
                template: '<span data-ezelement="ezembedinline" data-href="{href}" data-ezview="{view}">{content}</span>',
                requiredContent: 'span',

                upcast: (element) => {
                    return element.name === 'span' && element.attributes['data-ezelement'] === 'ezembedinline';
                },

                insertWrapper: function(wrapper) {
                    this.editor.insertElement(wrapper);
                },

                createEmbedPreviewNode: function() {
                    return document.createElement('span');
                },

                createEmbedPreview: function(title) {
                    return `
                        <svg class="ez-icon ez-icon--small ez-icon--secondary">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#tag"></use>
                        </svg>
                        <span class="ez-embed-content__title">${title}</span>
                    `;
                },
            });

            editor.widgets.add('ezembedinline', embedInlineDefinition);
        },
    });
})(window);
