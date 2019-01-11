import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnTableColumn extends AlloyEditor.ButtonTableColumn {
    static get key() {
        return 'eztablecolumn';
    }

    render() {
        let buttonCommandsList;
        let buttonCommandsListId;

        if (this.props.expanded) {
            buttonCommandsListId = 'tableColumnList';
            buttonCommandsList = (
                <AlloyEditor.ButtonCommandsList
                    commands={this._getCommands()}
                    editor={this.props.editor}
                    listId={buttonCommandsListId}
                    onDismiss={this.props.toggleDropdown}
                />
            );
        }

        return (
            <div className="ae-container ae-has-dropdown">
                <button
                    aria-expanded={this.props.expanded}
                    aria-label={AlloyEditor.Strings.column}
                    aria-owns={buttonCommandsListId}
                    className="ae-button ez-btn-ae"
                    onClick={this.props.toggleDropdown}
                    role="listbox"
                    tabIndex={this.props.tabIndex}
                    title={AlloyEditor.Strings.column}>
                    <svg className="ez-icon ez-btn-ae__icon">
                        <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#table-column" />
                    </svg>
                </button>
                {buttonCommandsList}
            </div>
        );
    }
}

AlloyEditor.Buttons[EzBtnTableColumn.key] = AlloyEditor.EzBtnTableColumn = EzBtnTableColumn;
eZ.addConfig('ezAlloyEditor.ezBtnTableColumn', EzBtnTableColumn);
