import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzToolbarAdd extends AlloyEditor.Toolbars.add {
    static get key() {
        return 'ezadd';
    }

    /**
     * Checks if anything was selected in editor.
     * There may be situation when editor has focus but none of its elements.
     *
     * @returns {Boolean}
     */
    checkIsNothingSelected() {
        const { selectionData } = this.props;

        if (!selectionData || !selectionData.region) {
            return false;
        }

        const { top, bottom, left, right } = selectionData.region;

        return !top && !bottom && !left && !right;
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @returns {Object} The content which should be rendered.
     */
    render() {
        const selectionData = this.props.selectionData;
        const nothingSelected = this.checkIsNothingSelected();
        const textSelected = selectionData && selectionData.text;

        if (textSelected || nothingSelected) {
            return null;
        }

        const buttons = this._getButtons();

        if (!buttons) {
            return null;
        }

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
