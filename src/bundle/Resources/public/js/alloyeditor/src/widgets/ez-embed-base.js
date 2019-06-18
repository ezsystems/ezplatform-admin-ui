const IMAGE_TYPE_CLASS = 'ez-embed-type-image';
const IS_LINKED_CLASS = 'is-linked';
const SHOW_EDIT_LINK_TOOLBAR_ATTR = 'data-show-edit-link-toolbar';
const DATA_ALIGNMENT_ATTR = 'ezalign';

const embedBaseDefinition = {
    defaults: {
        href: 'ezcontent://',
        content: 'home',
        view: 'embed',
    },
    draggable: false,
    template: '<div data-ezelement="ezembed" data-href="{href}" data-ezview="{view}">{content}</div>',
    requiredContent: 'div',

    upcast: (element) => {
        return element.name === 'div' && element.attributes['data-ezelement'] === 'ezembed';
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
     * when an embed widget has the focus and the `ezembed` command
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
        this.syncAlignment();
        this.getEzConfigElement();
        this.setWidgetContent('');
        this.cancelEditEvents();

        this.initEditMode();
    },

    getIdentifier() {
        return this.isImage() ? 'ezembedimage' : 'ezembed';
    },

    /**
     * Initializes the edit mode.
     *
     * @method initEditMode
     */
    initEditMode: function() {
        const contentId = this.getHref().replace('ezcontent://', '');

        if (!contentId) {
            return;
        }

        this.loadContent(contentId);
    },

    /**
     * Loads the content info.
     *
     * @method loadContent
     * @param {String} contentId The content id
     */
    loadContent: function(contentId) {
        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
        const body = JSON.stringify({
            ViewInput: {
                identifier: `embed-load-content-info-${contentId}`,
                public: false,
                ContentQuery: {
                    Criteria: {},
                    FacetBuilders: {},
                    SortClauses: {},
                    Filter: { ContentIdCriterion: `${contentId}` },
                    limit: 1,
                    offset: 0,
                },
            },
        });
        const request = new Request('/api/ezp/v2/views', {
            method: 'POST',
            headers: {
                Accept: 'application/vnd.ez.api.View+json; version=1.1',
                'Content-Type': 'application/vnd.ez.api.ViewInput+json; version=1.1',
                'X-Siteaccess': siteaccess,
                'X-CSRF-Token': token,
            },
            body,
            mode: 'same-origin',
            credentials: 'same-origin',
        });

        fetch(request)
            .then((response) => response.json())
            .then(this.handleContentLoaded.bind(this))
            .catch((error) => window.eZ.helpers.notification.showErrorNotification(error));
    },

    /**
     * Loads the image variation.
     *
     * @method loadImageVariation
     * @param {String} variationHref The variation href
     */
    loadImageVariation: function(variationHref) {
        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
        const request = new Request(variationHref, {
            method: 'GET',
            headers: {
                Accept: 'application/vnd.ez.api.ContentImageVariation+json',
                'X-Siteaccess': siteaccess,
                'X-CSRF-Token': token,
            },
            credentials: 'same-origin',
            mode: 'same-origin',
        });

        fetch(request)
            .then((response) => response.json())
            .then((imageData) => this.renderEmbedImagePreview(imageData.ContentImageVariation.uri));
    },

    /**
     * Finds the ezimage field.
     *
     * @method findEzimageField
     * @returns {Object}
     */
    findEzimageField(fields) {
        return fields.find((field) => field.fieldTypeIdentifier === 'ezimage');
    },

    /**
     * Handles loading the content info.
     *
     * @method handleContentLoaded
     * @param {Object} hits The result of content search
     */
    handleContentLoaded: function(hits) {
        const isEmbedImage = this.element.$.classList.contains(IMAGE_TYPE_CLASS);
        const content = hits.View.Result.searchHits.searchHit[0].value.Content;

        if (isEmbedImage) {
            const fieldImage = this.findEzimageField(content.CurrentVersion.Version.Fields.field);

            if (!fieldImage || !fieldImage.fieldValue) {
                this.renderEmbedPreview(content.Name);

                return;
            }

            const size = this.getConfig('size');
            const variationHref = fieldImage.fieldValue.variations[size].href;

            this.variations = fieldImage.fieldValue.variations;

            this.loadImageVariation(variationHref);
        } else {
            this.renderEmbedPreview(content.Name);
        }
    },

    /**
     * Loads image preview from current version href
     *
     * @method loadImagePreviewFromCurrentVersion
     * @param {String} currentVersionHref The current version href
     * @param {String} contnetName The content name
     */
    loadImagePreviewFromCurrentVersion: function(currentVersionHref, contentName) {
        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
        const request = new Request(currentVersionHref, {
            method: 'GET',
            headers: {
                Accept: 'application/vnd.ez.api.Version+json',
                'X-Siteaccess': siteaccess,
                'X-CSRF-Token': token,
            },
            credentials: 'same-origin',
            mode: 'same-origin',
        });

        fetch(request)
            .then((response) => response.json())
            .then((data) => {
                const fieldImage = this.findEzimageField(data.Version.Fields.field);

                if (!fieldImage || !fieldImage.fieldValue) {
                    contentName = contentName ? contentName : '';

                    this.renderEmbedPreview(contentName);

                    return;
                }

                const size = this.getConfig('size');
                const variationHref = fieldImage.fieldValue.variations[size].href;

                this.variations = fieldImage.fieldValue.variations;

                this.loadImageVariation(variationHref);
            });
    },

    createEmbedPreviewNode: function() {
        return document.createElement('p');
    },

    createEmbedPreview: function(title) {
        return `
            <svg class="ez-icon ez-icon--medium ez-icon--secondary">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#embed"></use>
            </svg>
            <span class="ez-embed-content__title">${title}</span>
        `;
    },

    /**
     * Renders the embed preview
     *
     * @method renderEmbedPreview
     * @param {String} title The content title
     */
    renderEmbedPreview: function(title) {
        const elementNode = this.createEmbedPreviewNode();
        const escapedTitle = eZ.helpers.text.escapeHTML(title);
        const template = this.createEmbedPreview(escapedTitle);

        elementNode.classList.add('ez-embed-content');
        elementNode.innerHTML = template;

        this.setWidgetContent(elementNode);
    },

    /**
     * Renders the embed image preview
     *
     * @method renderEmbedImagePreview
     * @param {String} imageUri The image uri
     */
    renderEmbedImagePreview: function(imageUri) {
        const elementNode = document.createElement('img');

        elementNode.setAttribute('src', imageUri);

        this.setWidgetContent(elementNode);

        if (this.element.hasClass(IS_LINKED_CLASS)) {
            this.renderLinkedIcon();
        }
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
     */
    syncAlignment: function() {
        const align = this.element.data(DATA_ALIGNMENT_ATTR);

        if (align) {
            this._setAlignment(align);
        } else {
            this._unsetAlignment();
        }
    },

    /**
     * Sets the alignment of the embed widget to `type`. The
     * alignment is set by adding the `data-ezalign` attribute
     * on the widget wrapper and the widget element.
     *
     * @method _setAlignment
     * @param {String} type
     */
    _setAlignment: function(type) {
        this.wrapper.data(DATA_ALIGNMENT_ATTR, type);
        this.element.data(DATA_ALIGNMENT_ATTR, type);
    },

    /**
     * Sets the alignment of the embed widget to `type` and fires
     * the corresponding `editorInteraction` event.
     *
     * @method setAlignment
     * @param {String} type
     */
    setAlignment: function(type) {
        this._setAlignment(type);
        this.fireEditorInteraction('setAlignment' + type);
    },

    /**
     * Removes the alignment of the widget.
     *
     * @method _unsetAlignment
     */
    _unsetAlignment: function() {
        this.wrapper.data(DATA_ALIGNMENT_ATTR, false);
        this.element.data(DATA_ALIGNMENT_ATTR, false);
    },

    /**
     * Removes the alignment of the widget and fires the
     * corresponding `editorInteraction` event.
     *
     * @method unsetAlignment
     */
    unsetAlignment: function() {
        this._unsetAlignment();
        this.fireEditorInteraction('unsetAlignment');
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
     * Set the embed as an embed representing an image
     *
     * @method setImageType
     * @return {CKEDITOR.plugins.widget}
     */
    setImageType: function() {
        this.element.addClass(IMAGE_TYPE_CLASS);

        return this;
    },

    /**
     * Check whether the embed widget represents an image or not.
     *
     * @method isImage
     * @return {Boolean}
     */
    isImage: function() {
        return this.element.hasClass(IMAGE_TYPE_CLASS);
    },

    /**
     * Sets the `href` of the embed is URI to the embed content or
     * location. (ezcontent://32 for instance).
     *
     * @method setHref
     * @param {String} href
     * @return {CKEDITOR.plugins.widget}
     */
    setHref: function(href) {
        this.element.data('href', href);

        return this;
    },

    /**
     * Returns the `href`of the embed.
     *
     * @method getHref
     * @return {String}
     */
    getHref: function() {
        return this.element.data('href');
    },

    /**
     * Sets the widget content. It makes sure the config element is
     * not overwritten.
     *
     * @method setWidgetContent
     * @param {String|CKEDITOR.dom.node} content
     * @return {CKEDITOR.plugins.widget}
     */
    setWidgetContent: function(content) {
        let element = this.element.getFirst();
        let next;

        while (element) {
            next = element.getNext();

            const isEzElement = element.data && element.data('ezelement');
            const isAnchorIcon = element.$.classList && element.$.classList.contains('ez-icon--anchor');
            const shouldRemove = !(isEzElement || isAnchorIcon);

            if (shouldRemove) {
                element.remove();
            }

            element = next;
        }

        if (content instanceof CKEDITOR.dom.node) {
            this.element.append(content);
        } else {
            this.element.appendText(content);
        }

        return this;
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
     * Sets a config value under the `key` for the embed.
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

        return this;
    },

    /**
     * Returns the config value for the `key` or undefined if the
     * config key is not found.
     *
     * @method getConfig
     * @return {String}
     */
    getConfig: function(key) {
        const valueElement = this.getValueElement(key);

        return valueElement ? valueElement.getText() : undefined;
    },

    /**
     * Returns the Element holding the config under `key`
     *
     * @method getValueElement
     * @param {String} key
     * @return {CKEDITOR.dom.element}
     */
    getValueElement: function(key) {
        return this.element.findOne('[data-ezelement="ezvalue"][data-ezvalue-key="' + key + '"]');
    },

    /**
     * Returns the element used as a container the config values. if
     * it does not exist, it is created.
     *
     * @method getEzConfigElement
     * @return {CKEDITOR.dom.element}
     */
    getEzConfigElement: function() {
        let config = this.element.findOne('[data-ezelement="ezconfig"]');

        if (!config) {
            config = new CKEDITOR.dom.element('span');
            config.data('ezelement', 'ezconfig');
            this.element.append(config, true);
        }

        return config;
    },

    /**
     * Returns the element used as a container the link values. if
     * it does not exist, it is created.
     *
     * @method getEzLinkElement
     * @return {CKEDITOR.dom.element}
     */
    getEzLinkElement: function() {
        let link = this.element.findOne('[data-ezelement="ezlink"]');

        if (!link) {
            link = new CKEDITOR.dom.element('a');
            link.$.innerHTML = ' ';
            link.setAttribute('data-ezelement', 'ezlink');
            link.setAttribute('data-ez-temporary-link', true);
            this.element.append(link);
        }

        return link;
    },

    /**
     * Gets the link attribute
     *
     * @method getEzLinkAttribute
     * @param {String} attribute
     * @return {String}
     */
    getEzLinkAttribute: function(attribute) {
        const link = this.getEzLinkElement();

        return link.getAttribute(attribute);
    },

    /**
     * Sets the link attribute
     *
     * @method getEzLinkAttribute
     * @param {String} attribute
     * @param {String} value
     */
    setEzLinkAttribute: function(attribute, value) {
        const link = this.getEzLinkElement();

        link.setAttribute(attribute, value);
    },

    /**
     * Removes the link attribute
     *
     * @method removeEzLinkAttribute
     * @param {String} attribute
     */
    removeEzLinkAttribute: function(attribute) {
        const link = this.getEzLinkElement();

        link.removeAttribute(attribute);
    },

    /**
     * Sets the link edit state
     *
     * @method setLinkEditState
     */
    setLinkEditState: function() {
        this.element.setAttribute(SHOW_EDIT_LINK_TOOLBAR_ATTR, true);
    },

    /**
     * Removes the link edit state
     *
     * @method removeLinkEditState
     */
    removeLinkEditState: function() {
        this.element.removeAttribute(SHOW_EDIT_LINK_TOOLBAR_ATTR);
    },

    /**
     * Checks if widget is in link edit state
     *
     * @method isEditingLink
     * @return {Boolean}
     */
    isEditingLink: function() {
        return this.element.hasAttribute(SHOW_EDIT_LINK_TOOLBAR_ATTR);
    },

    /**
     * Sets the is linked state
     *
     * @method setIsLinkedState
     */
    setIsLinkedState: function() {
        this.element.$.classList.add(IS_LINKED_CLASS);
        this.renderLinkedIcon();
    },

    /**
     * Removes the is linked state
     *
     * @method removeIsLinkedState
     */
    removeIsLinkedState: function() {
        this.element.$.classList.remove(IS_LINKED_CLASS);
        this.removeLinkedIcon();
    },

    /**
     * Renders the linked icon
     *
     * @method renderLinkedIcon
     */
    renderLinkedIcon: function() {
        const iconWrapper = new CKEDITOR.dom.element('span');
        const icon = `
            <svg class="ez-icon ez-icon--medium ez-icon--light">
                <use xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#link"></use>
            </svg>
        `;

        if (this.element.findOne('.ez-embed__icon-wrapper')) {
            return;
        }

        iconWrapper.$.classList.add('ez-embed__icon-wrapper');
        iconWrapper.$.innerHTML = icon;

        this.element.append(iconWrapper);
    },

    /**
     * Removes the linked icon
     *
     * @method removeLinkedIcon
     */
    removeLinkedIcon: function() {
        const iconWrapper = this.element.findOne('.ez-embed__icon-wrapper');

        if (iconWrapper) {
            iconWrapper.remove();
        }
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

export default embedBaseDefinition;
