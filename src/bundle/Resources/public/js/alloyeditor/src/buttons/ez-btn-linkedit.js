import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnLinkEdit extends Component {
    constructor(props) {
        super(props);

        this.state = this.getInitialState()
    }

    static get key() {
        return 'ezlinkedit';
    }

    componentWillReceiveProps(nextProps) {
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
        const linkUtils = new CKEDITOR.Link(this.props.editor.get('nativeEditor'), {appendProtocol: false});
        let link = linkUtils.getFromSelection();
        let href = '';
        let target = '';
        let title = ''
        let isTemporary = false;

        if (link) {
            href = link.getAttribute('href');
            target = link.hasAttribute('target') ? link.getAttribute('target') : target;
            title = link.getAttribute('title');
            isTemporary = link.hasAttribute('data-ez-temporary-link');
        } else {
            linkUtils.create(href, {"data-ez-temporary-link": true});
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

    /**
     * Runs the Universal Discovery Widget so that the user can pick a
     * Content.
     *
     * @method selectContent
     * @protected
     */
    selectContent() {
        const openUDW = () => {
            const selectable = this.props.udwIsSelectableMethod;
            const udwContainer = document.querySelector('#react-udw');
            const token = document.querySelector('meta[name="CSRF-Token"]').content;
            const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
            const config = JSON.parse(document.querySelector(`[data-udw-config-name="richtext_embed"]`).dataset.udwConfig);
            const udwOnConfirm = (items) => {
                this.state.element.setAttribute(
                    'href', 'ezlocation://' + items[0].id
                );
                this.focusEditedLink();

                ReactDOM.unmountComponentAtNode(udwContainer);
            };

            ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, Object.assign({
                onConfirm: udwOnConfirm,
                onCancel: () => ReactDOM.unmountComponentAtNode(udwContainer),
                confirmLabel: 'Select content',
                title: 'Select content',
                multiple: false,
                startingLocationId: window.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
                restInfo: {token, siteaccess},
            }, config)), udwContainer);
        };

        this.setState({
            discoveringContent: true,
        }, openUDW.bind(this));
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
                region: this.getRegion()
            }
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
        return (
            <div className="ez-ae-edit-link__row">
                <div className="ez-ae-edit-link__block ez-ae-edit-link__block--udw">
                    <button className="ez-ae-button ez-btn-ae ez-btn-ae--udw btn btn-gray" onClick={this.selectContent.bind(this)}>
                        Select content
                    </button>
                </div>
                <div className="ez-ae-edit-link__block ez-ae-edit-link__block--separator">
                    <span className="ez-ae-edit-link__text">or</span>
                </div>
                <div className="ez-ae-edit-link__block ez-ae-edit-link__block--url">
                    <label className="ez-ae-edit-link__label">Link to</label>
                    <input className="ae-input ez-ae-edit-link__input"
                        onChange={this.setHref.bind(this)} onKeyDown={this.handleKeyDown.bind(this)}
                        placeholder="Type or paste link here"
                        type="text" value={this.state.linkHref}
                    ></input>
                    <button aria-label={AlloyEditor.Strings.clearInput}
                        className="ez-btn-ae ez-btn-ae--clear-link ae-button ae-icon-remove"
                        onClick={this.clearLink.bind(this)} title={AlloyEditor.Strings.clear}
                    ></button>
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

        return (
            <div className="ez-ae-edit-link__row">
                <div className="ez-ae-edit-link__block ez-ae-edit-link__block--title">
                    <label className="ez-ae-edit-link__label">Title</label>
                    <input type="text"
                        className="ae-input ez-ae-edit-link__input" onChange={this.setTitle.bind(this)}
                        value={this.state.linkTitle}
                    ></input>
                </div>
                <div className="ez-ae-edit-link__block ez-ae-edit-link__block--target">
                    <span className="ez-ae-edit-link__text">Open in:</span>
                    <div className="ez-ae-edit-link__choice">
                        <label htmlFor="ez-ae-link-target-same" className="ez-ae-edit-link__label ez-ae-edit-link__label--same-tab">
                            <input type="radio" name="target" id="ez-ae-link-target-same"
                                value='' defaultChecked={target === ''}
                                onChange={this.setTarget.bind(this)}
                            />
                            <span>Same tab</span>
                        </label>
                        <label htmlFor="ez-ae-link-target-blank" className="ez-ae-edit-link__label ez-ae-edit-link__label--new-tab">
                            <input type="radio" name="target" id="ez-ae-link-target-blank"
                                value="_blank" defaultChecked={target === "_blank"}
                                onChange={this.setTarget.bind(this)}
                            />
                            <span>New tab</span>
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
        return (
            <div className="ez-ae-edit-link__row ez-ae-edit-link__row--actions">
                <div className="ez-ae-edit-link__block ez-ae-edit-link__block--actions">
                    <button className="ez-btn-ae ez-btn-ae--remove-link btn btn-gray"
                        disabled={this.state.isTemporary} onClick={this.removeLink.bind(this)}>
                        Remove
                    </button>
                    <button className="ez-btn-ae ez-btn-ae--save-link btn btn-secondary"
                        disabled={!this.state.linkHref} onClick={this.saveLink.bind(this)}>
                        Save
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
        this.setState({linkHref: ''});
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

        if (event.keyCode === 13 && event.target.value ) {
            this.saveLink();
        } else if (event.keyCode === 27) {
            const editor = this.props.editor.get('nativeEditor');
            new CKEDITOR.Link(editor).advanceSelection();
            editor.fire('actionPerformed', this);
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
        this.setState({linkHref: event.target.value});
    }

    /**
     * Sets the link title
     *
     * @method setTitle
     * @param {SyntheticEvent} event The change event.
     */
    setTitle(event) {
        this.setState({linkTitle: event.target.value});
    }

    /**
     * Sets the target of the link
     *
     * @method setTarget
     * @param {SyntheticEvent} event The change event.
     */
    setTarget(event) {
        this.setState({linkTarget: event.target.value});
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

        linkUtils.remove(this.state.element, {advance: true});

        selection.selectBookmarks(bookmarks);

        this.props.cancelExclusive();

        editor.fire('actionPerformed', this);
    }

    /**
     * Saves the link with the current href, title and target.
     *
     * @method saveLink
     */
    saveLink() {
        this.setState({
            isTemporary: false,
        }, () => this.updateLink());
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
            "data-ez-temporary-link": this.state.isTemporary ? true : null,
        };
        const modifySelection = {advance: true};

        if (this.state.linkHref) {
            linkAttrs.href = this.state.linkHref;
            linkUtils.update(linkAttrs, this.state.element, modifySelection);

            editor.fire('actionPerformed', this);
        }

        // We need to cancelExclusive with the bound parameters in case the
        // button is used inside another component in exclusive mode (such
        // is the case of the link button)
        this.props.cancelExclusive();
    }
}

AlloyEditor.Buttons[EzBtnLinkEdit.key] = AlloyEditor.ButtonLinkEdit = EzBtnLinkEdit;
