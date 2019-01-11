import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnTableRemove extends AlloyEditor.ButtonTableRemove {
    static get key() {
        return 'eztableremove';
    }

    render() {
        return (
            <button
                aria-label={AlloyEditor.Strings.deleteTable}
                className="ae-button ez-btn-ae"
                data-type="button-table-remove"
                onClick={this._removeTable}
                tabIndex={this.props.tabIndex}
                title={AlloyEditor.Strings.deleteTable}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#trash" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnTableRemove.key] = AlloyEditor.EzBtnTableRemove = EzBtnTableRemove;
eZ.addConfig('ezAlloyEditor.ezBtnTableRemove', EzBtnTableRemove);
