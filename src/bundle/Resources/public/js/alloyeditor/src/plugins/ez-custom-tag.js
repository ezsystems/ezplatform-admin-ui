(function (global) {
    const DATA_ALIGNMENT_ATTR = 'ezalign';

    if (CKEDITOR.plugins.get('ezcustomtag')) {
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
    CKEDITOR.plugins.add('ezcustomtag', {
        requires: 'widget,ezaddcontent',

        init: function (editor) {
            editor.widgets.add('ezcustomtag', {
                defaults: {
                    name: "customtag",
                },
                draggable: false,
                template: '<div data-ezelement="eztemplate" data-ezname="{name}"></div>',
                requiredContent: 'div',

                upcast: (element) => {
                    return (
                        element.name === 'div' &&
                        element.attributes['data-ezelement'] === 'eztemplate'
                    );
                },

                /**
                 * Insert an `ezembed` widget in the editor. It overrides the
                 * default implementation to make sure that in the case where an
                 * embed widget is focused, a new one is added after it.
                 *
                 * @method insert
                 */
                insert: function () {
                    var element = CKEDITOR.dom.element.createFromHtml(this.template.output(this.defaults)),
                        wrapper = editor.widgets.wrapElement(element, this.name),
                        temp = new CKEDITOR.dom.documentFragment(wrapper.getDocument()),
                        instance;

                    temp.append(wrapper);
                    editor.widgets.initOn(element, this.name);
                    editor.eZ.appendElement(wrapper);

                    instance = editor.widgets.getByElement(wrapper);
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
                edit: function () {
                    this.insert();
                },

                init: function () {
                    this.on('focus', this.fireEditorInteraction);
                    this.syncAlignment();
                    this.getEzContentElement();
                    this.getEzConfigElement();
                    this.cancelEditEvents();
                },

                /**
                 * Sets the `name` of the custom tag.
                 *
                 * @method setName
                 * @param {String} name
                 * @return {CKEDITOR.plugins.widget}
                 */
                setName: function (name) {
                    this.element.data('ezname', name);

                    return this;
                },

                /**
                 * Cancels the widget events that trigger the `edit` event as
                 * an embed widget can not be edited in a *CKEditor way*.
                 *
                 * @method cancelEditEvents
                 */
                cancelEditEvents: function () {
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
                syncAlignment: function () {
                    const align = this.element.data(DATA_ALIGNMENT_ATTR);

                    if (align) {
                        this.setAlignment(align);
                    } else {
                        this.unsetAlignment();
                    }
                },

                /**
                 * Sets the alignment of the embed widget to `type` and fires
                 * the corresponding `editorInteraction` event.
                 *
                 * @method setAlignment
                 * @param {String} type
                 */
                setAlignment: function (type) {
                    this.wrapper.data(DATA_ALIGNMENT_ATTR, type);
                    this.element.data(DATA_ALIGNMENT_ATTR, type);
                },

                /**
                 * Removes the alignment of the widget and fires the
                 * corresponding `editorInteraction` event.
                 *
                 * @method unsetAlignment
                 */
                unsetAlignment: function () {
                    this.wrapper.data(DATA_ALIGNMENT_ATTR, false);
                    this.element.data(DATA_ALIGNMENT_ATTR, false);
                },

                /**
                 * Checks whether the embed is aligned with `type` alignment.
                 *
                 * @method isAligned
                 * @param {String} type
                 * @return {Boolean}
                 */
                isAligned: function (type) {
                    return this.wrapper.data(DATA_ALIGNMENT_ATTR) === type;
                },

                /**
                 * Sets the widget content.
                 *
                 * @method setWidgetContent
                 * @param {String|CKEDITOR.dom.node} content
                 * @return {CKEDITOR.plugins.widget}
                 */
                setWidgetContent: function (content) {
                    const ezContent = this.element.findOne('[data-ezelement="ezcontent"]');
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
                setConfig: function (key, value) {
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
                 * Returns the config value for the `key` or empty string if the
                 * config key is not found.
                 *
                 * @method getConfig
                 * @return {String}
                 */
                getConfig: function (key) {
                    const valueElement = this.getValueElement(key);

                    return valueElement ? valueElement.getText() : '';
                },

                clearConfig: function () {
                    const config = this.getEzConfigElement();

                    while (config.firstChild) {
                        config.removeChild(config.firstChild);
                    }
                },

                getContent: function (key) {
                    const contentElement = this.getEzContentElement();

                    return contentElement ? contentElement.getText() : '';
                },

                /**
                 * Returns the Element holding the config under `key`
                 *
                 * @method getValueElement
                 * @param {String} key
                 * @return {CKEDITOR.dom.element}
                 */
                getValueElement: function (key) {
                    return this.element.findOne('[data-ezelement="ezvalue"][data-ezvalue-key="' + key + '"]');
                },

                /**
                 * Returns the element used as a container the config values. if
                 * it does not exist, it is created.
                 *
                 * @method getEzConfigElement
                 * @return {CKEDITOR.dom.element}
                 */
                getEzConfigElement: function () {
                    let config = this.element.findOne('[data-ezelement="ezconfig"]');

                    if (!config) {
                        config = new CKEDITOR.dom.element('span');
                        config.data('ezelement', 'ezconfig');
                        this.element.append(config);
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
                getEzContentElement: function () {
                    let content = this.element.findOne('[data-ezelement="ezcontent"]');

                    if (!content) {
                        content = new CKEDITOR.dom.element('div');
                        content.data('ezelement', 'ezcontent');
                        this.element.append(content, true);
                    }

                    return content;
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
                fireEditorInteraction: function (evt) {
                    const wrapperRegion = this.getWrapperRegion();
                    const name = evt.name || evt;
                    const event = {
                        editor: editor,
                        target: this.element.$,
                        name: 'widget' + name,
                        pageX: wrapperRegion.left,
                        pageY: wrapperRegion.top + wrapperRegion.height,
                    };

                    editor.focus();
                    this.focus();

                    editor.fire('editorInteraction', {
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
                moveAfter: function (element) {
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
                moveBefore: function (element) {
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
                getWrapperRegion: function () {
                    const scroll = this.wrapper.getWindow().getScrollPosition();
                    const region = this.wrapper.getClientRect();

                    region.top += scroll.y;
                    region.bottom += scroll.y;
                    region.left += scroll.x;
                    region.right += scroll.x;
                    region.direction = CKEDITOR.SELECTION_TOP_TO_BOTTOM;

                    return region;
                },
            });
        },
    });
})(window);
