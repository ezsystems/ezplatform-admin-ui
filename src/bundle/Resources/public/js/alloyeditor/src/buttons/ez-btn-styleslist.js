import React from 'react';
import AlloyEditor from 'alloyeditor';
import EzButtonDropdown from './ez-btn-dropdown';

export default class EzButtonStylesList extends AlloyEditor.ButtonStylesList {
    /**
     * Lifecycle. Renders the UI of the list.
     *
     * @instance
     * @memberof ButtonStylesList
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        let removeStylesItem;

        if (this.props.showRemoveStylesItem) {
            removeStylesItem = <AlloyEditor.ButtonStylesListItemRemove editor={this.props.editor} onDismiss={this.props.toggleDropdown} />;
        }

        return (
            <EzButtonDropdown {...this.props}>
                {removeStylesItem}

                <AlloyEditor.ButtonsStylesListHeader name={AlloyEditor.Strings.blockStyles} styles={this._blockStyles} />
                {this._renderStylesItems(this._blockStyles)}

                <AlloyEditor.ButtonsStylesListHeader name={AlloyEditor.Strings.inlineStyles} styles={this._inlineStyles} />
                {this._renderStylesItems(this._inlineStyles)}

                <AlloyEditor.ButtonsStylesListHeader name={AlloyEditor.Strings.objectStyles} styles={this._objectStyles} />
                {this._renderStylesItems(this._objectStyles)}
            </EzButtonDropdown>
        );
    }
}

AlloyEditor.ButtonStylesList = AlloyEditor.EzButtonStylesList = EzButtonStylesList;

const eZ = (window.eZ = window.eZ || {});

eZ.ezAlloyEditor = eZ.ezAlloyEditor || {};
eZ.ezAlloyEditor.EzButtonStylesList = EzButtonStylesList;
