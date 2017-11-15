import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzToolbarAdd extends AlloyEditor.Toolbars.add {
    static get key() {
        return 'ezadd';
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
                aria-label={AlloyEditor.Strings.add} className={className}
                data-tabindex={this.props.config.tabIndex || 0} onFocus={this.focus}
                onKeyDown={this.handleKey} role="toolbar" tabIndex="-1"
            >
                <div className="ae-container">
                    {buttons}
                </div>
            </div>
        );
    }
}

 AlloyEditor.Toolbars[EzToolbarAdd.key] = EzToolbarAdd;
