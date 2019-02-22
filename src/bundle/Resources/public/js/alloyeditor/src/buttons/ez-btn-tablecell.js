import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnTableCell extends AlloyEditor.ButtonTableCell {
    static get key() {
        return 'eztablecell';
    }

    render() {
        let buttonCommandsList;
        let buttonCommandsListId;

        if (this.props.expanded) {
            buttonCommandsListId = 'tableCellList';
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
                    aria-label={AlloyEditor.Strings.cell}
                    aria-owns={buttonCommandsListId}
                    className="ae-button ez-btn-ae"
                    onClick={this.props.toggleDropdown}
                    tabIndex={this.props.tabIndex}
                    title={AlloyEditor.Strings.cell}>
                    <svg className="ez-icon ez-btn-ae__icon">
                        <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#table-cell" />
                    </svg>
                </button>
                {buttonCommandsList}
            </div>
        );
    }
}

AlloyEditor.Buttons[EzBtnTableCell.key] = AlloyEditor.EzBtnTableCell = EzBtnTableCell;
eZ.addConfig('ezAlloyEditor.ezBtnTableCell', EzBtnTableCell);
