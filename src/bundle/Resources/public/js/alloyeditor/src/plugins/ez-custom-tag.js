import customTagBaseDefinition from '../widgets/ez-custom-tag-base';

CKEDITOR.dtd.$editable.span = 1;

(function(global) {
    if (CKEDITOR.plugins.get('ezcustomtag') && CKEDITOR.plugins.get('ezinlinecustomtag')) {
        return;
    }

    CKEDITOR.plugins.add('ezcustomtag', {
        requires: 'widget,ezaddcontent',

        init: function(editor) {
            const customTagDefinition = Object.assign({}, customTagBaseDefinition, { editor, global });

            editor.widgets.add('ezcustomtag', customTagDefinition);
        },
    });

    CKEDITOR.plugins.add('ezinlinecustomtag', {
        requires: 'widget,ezaddcontent',

        init: function(editor) {
            const inlineCustomTagDefinition = Object.assign({}, customTagBaseDefinition, {
                editor,
                global,
                template: `<span class="ez-custom-tag ez-custom-tag--content-visible" data-ezelement="eztemplateinline" data-ezname="{name}">
                        <span class="ez-custom-tag__icon-wrapper"></span>
                        <span data-ezelement="ezcontent">{content}</span>
                    </span>`,
                requiredContent: 'div',

                upcast: (element) => {
                    return (
                        element.name === 'span' &&
                        element.attributes['data-ezelement'] === 'eztemplateinline' &&
                        !element.attributes['data-eztype']
                    );
                },

                init: function() {
                    this.on('focus', this.fireEditorInteraction);
                    this.syncAlignment(true);
                    this.getEzConfigElement();
                    this.cancelEditEvents();
                    this.renderIcon();
                    this.initEnterHandler();
                },

                getIdentifier() {
                    return 'ezinlinecustomtag';
                },

                insertWrapper: function(wrapper) {
                    this.editor.insertElement(wrapper);
                },

                renderIcon: function() {
                    const customTagConfig = global.eZ.adminUiConfig.richTextCustomTags[this.getName()];

                    if (!customTagConfig) {
                        return;
                    }

                    const iconWrapper = this.getIconWrapper();
                    const icon = `
                        <svg class="ez-icon ez-icon--small ez-icon--secondary">
                            <use xlink:href=${customTagConfig.icon} />
                        </svg>
                    `;

                    iconWrapper.appendHtml(icon);
                },

                getIconWrapper: function() {
                    let iconWrapper = [...this.element.getChildren().$].find(
                        (child) => child.dataset && child.classList.contains('ez-custom-tag__icon-wrapper')
                    );

                    if (!iconWrapper) {
                        iconWrapper = new CKEDITOR.dom.element('span');
                        iconWrapper.addClass('ez-custom-tag__icon-wrapper');
                        this.element.append(iconWrapper, true);
                    } else {
                        iconWrapper = new CKEDITOR.dom.element(iconWrapper);
                    }

                    return iconWrapper;
                },
            });

            editor.widgets.add('ezinlinecustomtag', inlineCustomTagDefinition);
        },
    });
})(window);
