import React from 'react';
import AlloyEditor from 'alloyeditor';
import EzBtnImage from "./ez-btn-image";

export default class EzBtnDropdown extends AlloyEditor.ButtonDropdown {
    static get key() {
        return 'ezbtndropdown';
    }

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

AlloyEditor.Buttons[EzBtnDropdown.key] = AlloyEditor.EzBtnDropdown = EzBtnDropdown;
eZ.addConfig('ezAlloyEditor.ezBtnDropdown', EzBtnDropdown);
