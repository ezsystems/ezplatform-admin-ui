const DATA_ALIGNMENT_ATTR = 'ezalign';

const customTagBaseDefinition = {
    defaults: {
        name: 'customtag',
        content: '',
    },
    draggable: false,
    template:
        '<div class="ez-custom-tag ez-custom-tag--attributes-visible" data-ezelement="eztemplate" data-ezname="{name}"><div data-ezelement="ezcontent">{content}</div></div>',
    requiredContent: 'div',
    editables: {
        content: {
            selector: '[data-ezelement="ezcontent"]',
        },
    },
    setNameFireEditorInteractionTimeout: null,
    setAlignmentFireEditorInteractionTimeout: null,
    unsetAlignmentFireEditorInteractionTimeout: null,
    setConfigFireEditorInteractionTimeout: null,
    clearConfigFireEditorInteractionTimeout: null,

    upcast: (element) => {
        return element.name === 'div' && element.attributes['data-ezelement'] === 'eztemplate' && !element.attributes['data-eztype'];
    },

    insertWrapper: function(wrapper) {
        this.editor.eZ.appendElement(wrapper);
    },

    /**
     * Insert an `ezembed` widget in the editor. It overrides the
     * default implementation to make sure that in the case where an
     * embed widget is focused, a new one is added after it.
     *
     * @method insert
     */
    insert: function() {
        const element = CKEDITOR.dom.element.createFromHtml(this.template.output(this.defaults));
        const wrapper = this.editor.widgets.wrapElement(element, this.name);

        this.editor.widgets.initOn(element, this.name);

        this.insertWrapper(wrapper);

        const instance = this.editor.widgets.getByElement(wrapper);
        instance.ready = true;
        instance.fire('ready');
        instance.focus();
    },

    /**
     * It's not possible to *edit* an embed widget in AlloyEditor,
     * so `edit` directly calls `insert` instead. This is needed
     * because by default, the CKEditor engine calls this method
     * when an embed widget has the focus and the `ezcustomtag` command
     * is executed. In AlloyEditor, we want to insert a new widget,
     * not to `edit` the focused widget as the editing process is
     * provided by the style toolbar.
     *
     * @method edit
     */
    edit: function() {
        this.insert();
    },

    init: function() {
        this.on('focus', this.fireEditorInteraction);
        this.syncAlignment(true);
        this.renderAttributes();
        this.renderHeader();
        this.getEzContentElement();
        this.getEzConfigElement();
        this.cancelEditEvents();
        this.toggleState({
            currentTarget: {
                dataset: {
                    target: 'attributes',
                },
            },
        });
    },

    getIdentifier() {
        return 'ezcustomtag';
    },

    /**
     * Clear the node.
     *
     * @method clearNode
     * @param {Element} node
     */
    clearNode: function(node) {
        let element = node.getFirst();
        let next;

        while (element) {
            next = element.getNext();
            element.remove();
            element = next;
        }
    },

    /**
     * Renders the custom tag header.
     *
     * @method renderHeader
     */
    renderHeader: function() {
        const customTagConfig = global.eZ.adminUiConfig.richTextCustomTags[this.getName()];

        if (!customTagConfig) {
            return;
        }

        const header = this.getHeader();
        const template = `
                <div class="ez-custom-tag__header-label">
                    ${customTagConfig.label}
                </div>
                <div class="ez-custom-tag__header-btns">
                    <button class="btn ez-custom-tag__header-btn ez-custom-tag__header-btn--attributes" data-target="attributes">
                        <svg class="ez-icon ez-icon--small">
                            <use xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#list"></use>
                        </svg>
                    </button>
                    <button class="btn ez-custom-tag__header-btn ez-custom-tag__header-btn--content" data-target="content">
                        <svg class="ez-icon ez-icon--small">
                            <use xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#edit"></use>
                        </svg>
                    </button>
                </div>
        `;

        this.clearNode(header);

        header.appendHtml(template);

        this.attachButtonsListeners();
    },

    /**
     * Attaches event listeners to toggle state buttons.
     *
     * @method attachButtonsListeners
     */
    attachButtonsListeners: function() {
        const header = this.getHeader();
        const attributesBtn = header.findOne('.ez-custom-tag__header-btn--attributes');
        const contentBtn = header.findOne('.ez-custom-tag__header-btn--content');

        [attributesBtn, contentBtn].forEach((btn) => btn.$.addEventListener('click', this.toggleState.bind(this), false));
    },

    /**
     * Toggles the custom tag state.
     *
     * @method toggleState
     * @param {Event} event
     */
    toggleState: function(event) {
        const visibleElement = event.currentTarget.dataset.target;
        const classes = {
            attributes: 'ez-custom-tag--attributes-visible',
            content: 'ez-custom-tag--content-visible',
        };

        Object.entries(classes).forEach(([key, className]) => this.element.$.classList.toggle(className, key === visibleElement));
    },

    /**
     * Renders the custom tag attributes.
     *
     * @method renderAttributes
     */
    renderAttributes: function() {
        const customTagConfig = global.eZ.adminUiConfig.richTextCustomTags[this.getName()];

        if (!customTagConfig || !customTagConfig.attributes) {
            return;
        }
        const attributes = Object.keys(customTagConfig.attributes).reduce((total, attr) => {
            const value = this.getConfig(attr);

            return `${total}<p>${customTagConfig.attributes[attr].label}: ${value}</p>`;
        }, '');

        this.setWidgetAttributes(attributes);
    },

    /**
     * Sets the `name` of the custom tag.
     *
     * @method setName
     * @param {String} name
     * @return {CKEDITOR.plugins.widget}
     */
    setName: function(name) {
        this.element.data('ezname', name);

        window.clearTimeout(this.setNameFireEditorInteractionTimeout);
        this.setNameFireEditorInteractionTimeout = window.setTimeout(this.fireEditorInteraction.bind(this, 'nameUpdated'), 50);

        return this;
    },

    /**
     * Gets the `name` of the custom tag.
     *
     * @method getName
     * @return {CKEDITOR.plugins.widget}
     */
    getName: function() {
        return this.element.data('ezname');
    },

    /**
     * Cancels the widget events that trigger the `edit` event as
     * an embed widget can not be edited in a *CKEditor way*.
     *
     * @method cancelEditEvents
     */
    cancelEditEvents: function() {
        const cancel = (event) => event.cancel();

        this.on('doubleclick', cancel, null, null, 5);
        this.on('key', cancel, null, null, 5);
    },

    /**
     * Initializes the alignment on the widget wrapper if the widget
     * is aligned.
     *
     * @method syncAlignment
     * @param {Boolean} fireEditorInteractionPrevented
     */
    syncAlignment: function(fireEditorInteractionPrevented) {
        const align = this.element.data(DATA_ALIGNMENT_ATTR);

        if (align) {
            this.setAlignment(align, fireEditorInteractionPrevented);
        } else {
            this.unsetAlignment(fireEditorInteractionPrevented);
        }
    },

    /**
     * Sets the alignment of the embed widget to `type` and fires
     * the corresponding `editorInteraction` event.
     *
     * @method setAlignment
     * @param {String} type
     * @param {Boolean} fireEditorInteractionPrevented
     */
    setAlignment: function(type, fireEditorInteractionPrevented) {
        this.wrapper.data(DATA_ALIGNMENT_ATTR, type);
        this.element.data(DATA_ALIGNMENT_ATTR, type);

        if (!fireEditorInteractionPrevented) {
            window.clearTimeout(this.setAlignmentFireEditorInteractionTimeout);
            this.setAlignmentFireEditorInteractionTimeout = window.setTimeout(this.fireEditorInteraction.bind(this, 'aligmentUpdated'), 50);
        }
    },

    /**
     * Removes the alignment of the widget and fires the
     * corresponding `editorInteraction` event.
     *
     * @method unsetAlignment
     * @param {Boolean} fireEditorInteractionPrevented
     */
    unsetAlignment: function(fireEditorInteractionPrevented) {
        this.wrapper.data(DATA_ALIGNMENT_ATTR, false);
        this.element.data(DATA_ALIGNMENT_ATTR, false);

        if (!fireEditorInteractionPrevented) {
            window.clearTimeout(this.unsetAlignmentFireEditorInteractionTimeout);
            this.unsetAlignmentFireEditorInteractionTimeout = window.setTimeout(
                this.fireEditorInteraction.bind(this, 'aligmentRemoved'),
                50
            );
        }
    },

    /**
     * Checks whether the embed is aligned with `type` alignment.
     *
     * @method isAligned
     * @param {String} type
     * @return {Boolean}
     */
    isAligned: function(type) {
        return this.wrapper.data(DATA_ALIGNMENT_ATTR) === type;
    },

    /**
     * Sets the widget content.
     *
     * @method setWidgetContent
     * @param {String|CKEDITOR.dom.node} content
     * @return {CKEDITOR.plugins.widget}
     */
    setWidgetContent: function(content) {
        const ezContent = this.getEzContentElement();
        let element = ezContent.getFirst();
        let next;

        while (element) {
            next = element.getNext();
            element.remove();
            element = next;
        }

        if (content instanceof CKEDITOR.dom.node) {
            ezContent.append(content);
        } else {
            ezContent.appendHtml(content);
        }

        return this;
    },

    /**
     * Sets a config value under the `key` for the custom tag.
     *
     * @method setConfig
     * @param {String} key
     * @param {String} value
     * @return {CKEDITOR.plugins.widget}
     */
    setConfig: function(key, value) {
        let valueElement = this.getValueElement(key);

        if (!valueElement) {
            valueElement = new CKEDITOR.dom.element('span');
            valueElement.data('ezelement', 'ezvalue');
            valueElement.data('ezvalue-key', key);
            this.getEzConfigElement().append(valueElement);
        }

        valueElement.setText(value);

        window.clearTimeout(this.setConfigFireEditorInteractionTimeout);
        this.setConfigFireEditorInteractionTimeout = window.setTimeout(this.fireEditorInteraction.bind(this, 'configUpdated'), 50);

        return this;
    },

    /**
     * Sets the widget attributes.
     *
     * @method setWidgetAttributes
     * @param {String|CKEDITOR.dom.node} attributes
     * @return {CKEDITOR.plugins.widget}
     */
    setWidgetAttributes: function(attributes) {
        const ezAttributes = this.getEzAttributesElement();
        let element = ezAttributes.getFirst();
        let next;

        while (element) {
            next = element.getNext();
            element.remove();
            element = next;
        }

        if (attributes instanceof CKEDITOR.dom.node) {
            ezAttributes.append(attributes);
        } else {
            ezAttributes.appendHtml(attributes);
        }

        return this;
    },

    /**
     * Returns the config value for the `key` or empty string if the
     * config key is not found.
     *
     * @method getConfig
     * @return {String}
     */
    getConfig: function(key) {
        const valueElement = this.getValueElement(key);

        return valueElement ? valueElement.getText() : '';
    },

    clearConfig: function() {
        const config = this.getEzConfigElement();

        while (config.firstChild) {
            config.removeChild(config.firstChild);
        }

        window.clearTimeout(this.clearConfigFireEditorInteractionTimeout);
        this.clearConfigFireEditorInteractionTimeout = window.setTimeout(this.fireEditorInteraction.bind(this, 'configCleared'), 50);
    },

    /**
     * Returns the Element holding the config under `key`
     *
     * @method getValueElement
     * @param {String} key
     * @return {CKEDITOR.dom.element}
     */
    getValueElement: function(key) {
        return this.getEzConfigElement().findOne('[data-ezelement="ezvalue"][data-ezvalue-key="' + key + '"]');
    },

    /**
     * Returns the element used as a container the config values. if
     * it does not exist, it is created.
     *
     * @method getEzConfigElement
     * @return {CKEDITOR.dom.element}
     */
    getEzConfigElement: function() {
        let config = [...this.element.getChildren().$].find((child) => child.dataset && child.dataset.ezelement === 'ezconfig');

        if (!config) {
            config = new CKEDITOR.dom.element('span');
            config.data('ezelement', 'ezconfig');
            this.element.append(config);
        } else {
            config = new CKEDITOR.dom.element(config);
        }

        return config;
    },

    /**
     * Returns the element used as a container the content values. if
     * it does not exist, it is created.
     *
     * @method getEzContentElement
     * @return {CKEDITOR.dom.element}
     */
    getEzContentElement: function() {
        let content = [...this.element.getChildren().$].find((child) => child.dataset && child.dataset.ezelement === 'ezcontent');

        if (!content) {
            content = new CKEDITOR.dom.element('div');
            content.data('ezelement', 'ezcontent');
            this.element.append(content);
        } else {
            content = new CKEDITOR.dom.element(content);
        }

        return content;
    },

    /**
     * Returns the element used as a container the attributes values. if
     * it does not exist, it is created.
     *
     * @method getEzAttributesElement
     * @return {CKEDITOR.dom.element}
     */
    getEzAttributesElement: function() {
        let attributes = [...this.element.getChildren().$].find((child) => child.dataset && child.dataset.ezelement === 'ezattributes');

        if (!attributes) {
            attributes = new CKEDITOR.dom.element('div');
            attributes.data('ezelement', 'ezattributes');
            this.element.append(attributes, true);
        } else {
            attributes = new CKEDITOR.dom.element(attributes);
        }

        return attributes;
    },

    /**
     * Returns the element used as a container the header. if
     * it does not exist, it is created.
     *
     * @method getHeader
     * @return {CKEDITOR.dom.element}
     */
    getHeader: function() {
        let header = [...this.element.getChildren().$].find((child) => child.dataset && child.classList.contains('ez-custom-tag__header'));

        if (!header) {
            header = new CKEDITOR.dom.element('div');
            header.addClass('ez-custom-tag__header');
            this.element.append(header, true);
        } else {
            header = new CKEDITOR.dom.element(header);
        }

        return header;
    },

    /**
     * Fires the editorInteraction event so that AlloyEditor editor
     * UI remains visible and is updated. This method also computes
     * `selectionData.region` and the `pageX` and `pageY` properties
     * so that the add toolbar is correctly positioned on the
     * widget.
     *
     * @method fireEditorInteraction
     * @param {Object|String} evt this initial event info object or
     * the event name for which the `editorInteraction` is fired.
     */
    fireEditorInteraction: function(evt) {
        const wrapperRegion = this.getWrapperRegion();
        const name = evt.name || evt;
        const event = {
            editor: this.editor,
            target: this.element.$,
            name: 'widget' + name,
            pageX: wrapperRegion.left,
            pageY: wrapperRegion.top + wrapperRegion.height,
        };

        this.editor.focus();
        this.focus();

        this.editor.fire('editorInteraction', {
            nativeEvent: event,
            selectionData: {
                element: this.element,
                region: wrapperRegion,
            },
        });
    },

    /**
     * Moves the widget after the given element. It also fires the
     * `editorInteraction` event so that the UI can respond to that
     * change.
     *
     * @method moveAfter
     * @param {CKEDITOR.dom.element} element
     */
    moveAfter: function(element) {
        this.wrapper.insertAfter(element);
        this.fireEditorInteraction('moveAfter');
    },

    /**
     * Moves the widget before the given element. It also fires the
     * `editorInteraction` event so that the UI can respond to that
     * change.
     *
     * @method moveAfter
     * @param {CKEDITOR.dom.element} element
     */
    moveBefore: function(element) {
        this.wrapper.insertBefore(element);
        this.fireEditorInteraction('moveBefore');
    },

    /**
     * Returns the wrapper element region.
     *
     * @method getWrapperRegion
     * @private
     * @return {Object}
     */
    getWrapperRegion: function() {
        const scroll = this.wrapper.getWindow().getScrollPosition();
        const region = this.wrapper.getClientRect();

        region.top += scroll.y;
        region.bottom += scroll.y;
        region.left += scroll.x;
        region.right += scroll.x;
        region.direction = CKEDITOR.SELECTION_TOP_TO_BOTTOM;

        return region;
    },
};

export default customTagBaseDefinition;
