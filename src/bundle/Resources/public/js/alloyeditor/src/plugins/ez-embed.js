import embedBaseDefinition from '../widgets/ez-embed-base';

const ZERO_WIDTH_SPACE = '&#8203;';

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
                    console.warn('[DEPRECATED] canBeAdded method is deprecated');
                    console.warn('[DEPRECATED] it will be removed from ezplatform-admin-ui 2.0');
                    return true;
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

                getIdentifier() {
                    return 'ezembedinline';
                },

                insertWrapper: function(wrapper) {
                    this.editor.insertElement(wrapper);
                },

                createEmbedPreviewNode: function() {
                    return document.createElement('span');
                },
            });

            editor.widgets.add('ezembedinline', embedInlineDefinition);
        },
    });
})(window);
