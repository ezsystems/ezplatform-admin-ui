import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnLinkEdit extends Component {
    constructor(props) {
        super(props);

        this.state = this.getInitialState();
    }

    static get key() {
        return 'ezlinkedit';
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        this.setState(this.getInitialState());
    }

    componentWillUnmount() {
        if (!this.state.discoveringContent && this.state.isTemporary) {
            this.removeLink();
        }
    }

    /**
     * Lifecycle. Invoked once before the component is mounted.
     * The return value will be used as the initial value of this.state.
     *
     * @method getInitialState
     */
    getInitialState() {
        const linkUtils = new CKEDITOR.Link(this.props.editor.get('nativeEditor'), { appendProtocol: false });
        let link = linkUtils.getFromSelection();
        let href = '';
        let target = '';
        let title = '';
        let isTemporary = false;

        if (link) {
            href = link.getAttribute('href');
            target = link.hasAttribute('target') ? link.getAttribute('target') : target;
            title = link.getAttribute('title');
            isTemporary = link.hasAttribute('data-ez-temporary-link');
        } else {
            linkUtils.create(href, { 'data-ez-temporary-link': true });
            link = linkUtils.getFromSelection();
            isTemporary = true;
        }

        return {
            element: link,
            linkHref: href,
            linkTarget: target,
            linkTitle: title,
            isTemporary: isTemporary,
        };
    }

    udwOnConfirm(udwContainer, items) {
        this.state.element.setAttribute('href', 'ezlocation://' + items[0].id);

        this.invokeWithFixedScrollbar(() => {
            this.focusEditedLink();
        });

        ReactDOM.unmountComponentAtNode(udwContainer);
    }

    udwOnCancel(udwContainer) {
        this.invokeWithFixedScrollbar(() => {
            this.focusEditedLink();
        });

        ReactDOM.unmountComponentAtNode(udwContainer);
    }

    /**
     * Runs the Universal Discovery Widget so that the user can pick a
     * Content.
     *
     * @method selectContent
     * @protected
     */
    selectContent() {
        const openUDW = () => {
            const udwContainer = document.querySelector('#react-udw');
            const token = document.querySelector('meta[name="CSRF-Token"]').content;
            const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
            const config = JSON.parse(document.querySelector(`[data-udw-config-name="richtext_embed"]`).dataset.udwConfig);
            const title = Translator.trans(/*@Desc("Select content")*/ 'link_edit_btn.udw.title', {}, 'alloy_editor');
            const alloyEditorCallbacks = eZ.ezAlloyEditor.callbacks;
            const mergedConfig = Object.assign(
                {
                    onConfirm: this.udwOnConfirm.bind(this, udwContainer),
                    onCancel: this.udwOnCancel.bind(this, udwContainer),
                    title,
                    multiple: false,
                    startingLocationId: window.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
                    restInfo: { token, siteaccess },
                },
                config
            );

            if (alloyEditorCallbacks && typeof alloyEditorCallbacks.openUdw === 'function') {
                alloyEditorCallbacks.openUdw(mergedConfig);
            } else {
                ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, mergedConfig), udwContainer);
            }
        };

        this.setState(
            {
                discoveringContent: true,
            },
            openUDW.bind(this)
        );
    }

    /**
     * Gives the focus to the edited link by moving the caret in it.
     *
     * @method focusEditedLink
     * @protected
     */
    focusEditedLink() {
        const editor = this.props.editor.get('nativeEditor');

        editor.focus();
        editor.eZ.moveCaretToElement(editor, this.state.element);
        editor.fire('actionPerformed', this);

        this.showUI();
    }

    /**
     * Fires the editorInteraction event so that AlloyEditor editor
     * UI remains visible and is updated.
     *
     * @method showUI
     */
    showUI() {
        const nativeEditor = this.props.editor.get('nativeEditor');

        nativeEditor.fire('editorInteraction', {
            editor: this.props.editor,
            selectionData: {
                element: this.state.element,
                region: this.getRegion(),
            },
        });
    }

    /**
     * Returns the element region.
     *
     * @method getRegion
     * @return {Object}
     */
    getRegion() {
        const scroll = this.state.element.getWindow().getScrollPosition();
        const region = this.state.element.getClientRect();

        region.top += scroll.y;
        region.bottom += scroll.y;
        region.left += scroll.x;
        region.right += scroll.x;
        region.direction = CKEDITOR.SELECTION_TOP_TO_BOTTOM;

        return region;
    }

    /**
     * Lifecycle. Renders the row of the button.
     *
     * @method renderUDWRow
     * @return {Object} The content which should be rendered.
     */
    renderUDWRow() {
        const selectContentLabel = Translator.trans(
            /*@Desc("Select content")*/ 'link_edit_btn.button_row.select_content',
            {},
            'alloy_editor'
        );
        const separatorLabel = Translator.trans(/*@Desc("or")*/ 'link_edit_btn.button_row.separator', {}, 'alloy_editor');
        const linkToLabel = Translator.trans(/*@Desc("Link to:")*/ 'link_edit_btn.button_row.link_to', {}, 'alloy_editor');
        const selectLabel = Translator.trans(/*@Desc("Select:")*/ 'link_edit_btn.button_row.select', {}, 'alloy_editor');
        const blockPlaceholderText = Translator.trans(
            /*@Desc("Type or paste link here")*/ 'link_edit_btn.button_row.block.placeholder.text',
            {},
            'alloy_editor'
        );

        return (
            <div className="ez-ae-edit-link__row ez-ae-edit-link__row--udw">
                <div className="ez-ae-edit-link__block ez-ae-edit-link__block--udw">
                    <label className="ez-ae-edit-link__label">{selectLabel}</label>
                    <button className="ez-ae-button ez-btn-ae ez-btn-ae--udw btn btn-secondary" onClick={this.selectContent.bind(this)}>
                        {selectContentLabel}
                    </button>
                </div>
                <div className="ez-ae-edit-link__block ez-ae-edit-link__block--separator">
                    <span className="ez-ae-edit-link__text">{separatorLabel}</span>
                </div>
                <div className="ez-ae-edit-link__block ez-ae-edit-link__block--url">
                    <label className="ez-ae-edit-link__label">{linkToLabel}</label>
                    <input
                        className="ae-input ez-ae-edit-link__input"
                        onChange={this.setHref.bind(this)}
                        onKeyDown={this.handleKeyDown.bind(this)}
                        placeholder={blockPlaceholderText}
                        type="text"
                        value={this.state.linkHref}
                    />
                    <button
                        aria-label={AlloyEditor.Strings.clearInput}
                        className="ez-btn-ae ez-btn-ae--clear-link ae-button ae-icon-remove"
                        onClick={this.clearLink.bind(this)}
                        title={AlloyEditor.Strings.clear}
                    />
                </div>
            </div>
        );
    }

    /**
     * Lifecycle. Renders the row of the button.
     *
     * @method renderInfoRow
     * @return {Object} The content which should be rendered.
     */
    renderInfoRow() {
        const target = this.state.linkTarget;
        const title = Translator.trans(/*@Desc("Title:")*/ 'link_edit_btn.info_row.title', {}, 'alloy_editor');
        const openInLabel = Translator.trans(/*@Desc("Open in:")*/ 'link_edit_btn.info_row.open_in.label', {}, 'alloy_editor');
        const sameTabLabel = Translator.trans(/*@Desc("Same tab")*/ 'link_edit_btn.info_row.same_tab', {}, 'alloy_editor');
        const newTabLabel = Translator.trans(/*@Desc("New tab")*/ 'link_edit_btn.info_row.new_tab', {}, 'alloy_editor');

        return (
            <div className="ez-ae-edit-link__row ez-ae-edit-link__row--info">
                <div className="ez-ae-edit-link__block ez-ae-edit-link__block--title">
                    <label className="ez-ae-edit-link__label">{title}</label>
                    <input
                        type="text"
                        className="ae-input ez-ae-edit-link__input"
                        onChange={this.setTitle.bind(this)}
                        value={this.state.linkTitle}
                    />
                </div>
                <div className="ez-ae-edit-link__block ez-ae-edit-link__block--target">
                    <span className="ez-ae-edit-link__text">{openInLabel}</span>
                    <div className="ez-ae-edit-link__choice">
                        <label
                            htmlFor="ez-ae-link-target-same"
                            className="ez-ae-edit-link__label ez-ae-edit-link__label--same-tab"
                            title={sameTabLabel}>
                            <input
                                type="radio"
                                name="target"
                                id="ez-ae-link-target-same"
                                value=""
                                defaultChecked={target === ''}
                                onChange={this.setTarget.bind(this)}
                            />
                            <div className="ez-btn-ae__icon-wrapper">
                                <svg className="ez-icon ez-btn-ae__icon">
                                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#open-sametab" />
                                </svg>
                            </div>
                        </label>
                        <label
                            htmlFor="ez-ae-link-target-blank"
                            className="ez-ae-edit-link__label ez-ae-edit-link__label--new-tab"
                            title={newTabLabel}>
                            <input
                                type="radio"
                                name="target"
                                id="ez-ae-link-target-blank"
                                value="_blank"
                                defaultChecked={target === '_blank'}
                                onChange={this.setTarget.bind(this)}
                            />
                            <div className="ez-btn-ae__icon-wrapper">
                                <svg className="ez-icon ez-btn-ae__icon">
                                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#open-newtab" />
                                </svg>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        );
    }

    /**
     * Lifecycle. Renders the row of the button.
     *
     * @method renderActionRow
     * @return {Object} The content which should be rendered.
     */
    renderActionRow() {
        const removeLabel = Translator.trans(/*@Desc("Remove")*/ 'link_edit_btn.action_row.remove', {}, 'alloy_editor');
        const saveLabel = Translator.trans(/*@Desc("Save")*/ 'link_edit_btn.action_row.save', {}, 'alloy_editor');

        return (
            <div className="ez-ae-edit-link__row ez-ae-edit-link__row--actions">
                <div className="ez-ae-edit-link__block ez-ae-edit-link__block--actions">
                    <button
                        className="ez-btn-ae ez-btn-ae--remove-link btn"
                        disabled={this.state.isTemporary}
                        onClick={this.removeLink.bind(this)}
                        title={removeLabel}>
                        <svg className="ez-icon ez-btn-ae__icon">
                            <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#link-remove" />
                        </svg>
                    </button>
                    <button
                        className="ez-btn-ae ez-btn-ae--save-link btn"
                        disabled={!this.state.linkHref}
                        onClick={this.saveLink.bind(this)}
                        title={saveLabel}>
                        <svg className="ez-icon ez-btn-ae__icon">
                            <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#checkmark" />
                        </svg>
                    </button>
                </div>
            </div>
        );
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        let containerClass = 'ez-ae-edit-link';

        if (this.state.linkHref) {
            containerClass += ' is-linked';
        }

        return (
            <div className={containerClass}>
                {this.renderUDWRow()}
                {this.renderInfoRow()}
                {this.renderActionRow()}
            </div>
        );
    }

    /**
     * Clears the link input. This only changes the component internal
     * state, but does not affect the link element of the editor. Only the
     * removeLink and updateLink methods are translated to the editor
     * element.
     *
     * @method clearLink
     */
    clearLink() {
        this.setState({ linkHref: '' });
    }

    /**
     * Monitors key interaction inside the input element to respond to the
     * keys:
     * - Enter: Creates/updates the link.
     * - Escape: Discards the changes.
     *
     * @method handleKeyDown
     * @param {SyntheticEvent} event The keyboard event.
     */
    handleKeyDown(event) {
        if (event.keyCode === 13 || event.keyCode === 27) {
            event.preventDefault();
        }

        if (event.keyCode === 13 && event.target.value) {
            this.saveLink();
        } else if (event.keyCode === 27) {
            const editor = this.props.editor.get('nativeEditor');
            new CKEDITOR.Link(editor).advanceSelection();

            this.invokeWithFixedScrollbar(() => {
                editor.fire('actionPerformed', this);
            });
        }
    }

    /**
     * Updates the component state when the link input changes on user
     * interaction.
     *
     * @method setHref
     * @param {SyntheticEvent} event The change event.
     */
    setHref(event) {
        this.setState({ linkHref: event.target.value });
    }

    /**
     * Sets the link title
     *
     * @method setTitle
     * @param {SyntheticEvent} event The change event.
     */
    setTitle(event) {
        this.setState({ linkTitle: event.target.value });
    }

    /**
     * Sets the target of the link
     *
     * @method setTarget
     * @param {SyntheticEvent} event The change event.
     */
    setTarget(event) {
        this.setState({ linkTarget: event.target.value });
    }

    /**
     * Removes the link in the editor element.
     *
     * @method removeLink
     */
    removeLink() {
        const editor = this.props.editor.get('nativeEditor');
        const linkUtils = new CKEDITOR.Link(editor);
        const selection = editor.getSelection();
        const bookmarks = selection.createBookmarks();

        linkUtils.remove(this.state.element, { advance: true });

        selection.selectBookmarks(bookmarks);

        this.props.cancelExclusive();

        this.invokeWithFixedScrollbar(() => {
            editor.fire('actionPerformed', this);
        });

        editor.fire('customUpdate');
    }

    /**
     * Saves the link with the current href, title and target.
     *
     * @method saveLink
     */
    saveLink() {
        this.setState(
            {
                isTemporary: false,
            },
            () => this.updateLink()
        );
    }

    /**
     * Updates the link in the editor element. If the element didn't exist
     * previously, it will create a new <a> element with the href specified
     * in the link input.
     *
     * @method updateLink
     */
    updateLink() {
        const editor = this.props.editor.get('nativeEditor');
        const linkUtils = new CKEDITOR.Link(editor);
        const linkAttrs = {
            target: this.state.linkTarget,
            title: this.state.linkTitle,
            'data-ez-temporary-link': this.state.isTemporary ? true : null,
        };
        const modifySelection = { advance: true };

        if (this.state.linkHref) {
            linkAttrs.href = this.state.linkHref;
            linkUtils.update(linkAttrs, this.state.element, modifySelection);

            this.invokeWithFixedScrollbar(() => {
                editor.fire('actionPerformed', this);
            });
        }

        // We need to cancelExclusive with the bound parameters in case the
        // button is used inside another component in exclusive mode (such
        // is the case of the link button)
        this.props.cancelExclusive();
        this.showUI();
    }

    /**
     * Saves current scrollbar position, invokes callback function and scrolls
     * to the saved position afterward.
     *
     * @method invokeWithFixedScrollbar
     * @param {Function} callback invoked after saving current scrollbar position
     */
    invokeWithFixedScrollbar(callback) {
        if (navigator.userAgent.indexOf('Chrome') > -1) {
            const scrollY = window.pageYOffset;

            callback();
            window.scroll(window.pageXOffset, scrollY);
        } else {
            callback();
        }
    }
}

AlloyEditor.Buttons[EzBtnLinkEdit.key] = AlloyEditor.ButtonLinkEdit = EzBtnLinkEdit;
eZ.addConfig('ezAlloyEditor.ezBtnLinkEdit', EzBtnLinkEdit);
