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
        this._updatePosition();

        // In case of exclusive rendering, focus the first descendant (button)
        // so the user will be able to start interacting with the buttons immediately.
        if (this.props.renderExclusive) {
            this.focus();

            this._animate(this.setPosition);
        }
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const selectionData = this.props.selectionData;

        if (selectionData && selectionData.text) {
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
