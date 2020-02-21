import React from 'react';
import AlloyEditor from 'alloyeditor';

export default class EzBtnDropdown extends AlloyEditor.ButtonDropdown {
    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @instance
     * @memberof ButtonDropdown
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        return (
            <div className="ae-dropdown ae-arrow-box ae-arrow-box-top-left" onKeyDown={this.handleKey} tabIndex="0">
                <ul className="ae-listbox" role="listbox">
                    {this.props.children}
                </ul>
            </div>
        );
    }
}

const eZ = (window.eZ = window.eZ || {});

eZ.ezAlloyEditor = eZ.ezAlloyEditor || {};
eZ.ezAlloyEditor.EzBtnDropdown = EzBtnDropdown;
