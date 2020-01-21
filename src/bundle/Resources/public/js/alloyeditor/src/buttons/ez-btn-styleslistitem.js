import React from 'react';
import AlloyEditor from 'alloyeditor';

export default class EzBtnStylesListItem extends AlloyEditor.ButtonStylesListItem {
    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @instance
     * @memberof ButtonStylesListItem
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const className = this.props.name === this.props.activeStyle ? 'ae-toolbar-element active' : 'ae-toolbar-element';

        return (
            <button
                className={className}
                dangerouslySetInnerHTML={{ __html: this._preview }}
                onClick={() => {
                    this._onClick();
                    this.fireCustomUpdateEvent();
                }}
                tabIndex={this.props.tabIndex}
            />
        );
    }

    fireCustomUpdateEvent() {
        const nativeEditor = this.props.editor.get('nativeEditor');

        nativeEditor.fire('customUpdate');
    }
}

AlloyEditor.ButtonStylesListItem = AlloyEditor.EzBtnStylesListItem = EzBtnStylesListItem;
eZ.addConfig('ezAlloyEditor.ezBtnStylesListItem', EzBtnStylesListItem);
