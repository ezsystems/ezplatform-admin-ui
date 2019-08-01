import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzToolbarAdd extends AlloyEditor.Toolbars.add {
    static get key() {
        return 'ezadd';
    }

    constructor(props) {
        super(props);

        this.setPosition = this.setPosition.bind(this);
    }

    setPosition() {
        const domNode = ReactDOM.findDOMNode(this);
        const rect = this.props.editor.get('nativeEditor').element.$.getBoundingClientRect();

        new CKEDITOR.dom.element(domNode).setStyles({ left: `${rect.left}px` });
    }

    componentDidUpdate(prevProps, prevState) {
        const { selectionData } = this.props;

        this._updatePosition();

        if (selectionData && !selectionData.region.top) {
            this.setTopPosition();
        }

        // In case of exclusive rendering, focus the first descendant (button)
        // so the user will be able to start interacting with the buttons immediately.
        if (this.props.renderExclusive) {
            this.focus();

            this._animate(this.setPosition);
        }
    }

    setTopPosition() {
        const { editor } = this.props;
        const domNode = ReactDOM.findDOMNode(this);
        const nativeEditor = editor.get('nativeEditor');
        const path = nativeEditor.elementPath();
        const table = path && path.elements.find((element) => element.is('table'));
        const element = table || nativeEditor.element;

        const rect = element.$.getBoundingClientRect();

        new CKEDITOR.dom.element(domNode).setStyles({ top: `${rect.top}px` });
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const { selectionData, editor } = this.props;
        const path = editor.get('nativeEditor').elementPath();
        const isInlineCustomTag = path && path.contains((element) => element.$.dataset.ezelement === 'eztemplateinline');

        if ((selectionData && selectionData.text) || isInlineCustomTag) {
            return null;
        }

        const buttons = this._getButtons();
        const className = this._getToolbarClassName();

        return (
            <div
                aria-label={AlloyEditor.Strings.add}
                className={className}
                data-tabindex={this.props.config.tabIndex || 0}
                onFocus={this.focus}
                onKeyDown={this.handleKey}
                role="toolbar"
                tabIndex="-1">
                <div className="ae-container">{buttons}</div>
            </div>
        );
    }
}

AlloyEditor.Toolbars[EzToolbarAdd.key] = EzToolbarAdd;
eZ.addConfig('ezAlloyEditor.ezToolbarAdd', EzToolbarAdd);
