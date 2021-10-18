import React from 'react';
import PropTypes from 'prop-types';

const TableViewColumnsTogglerListElement = ({ label, isColumnVisible, toggleColumnVisibility, columnKey }) => {
    return (
        <li className="ibexa-popup-menu__item c-table-view-columns-toggler-list-element" onClick={() => toggleColumnVisibility(columnKey)}>
            <div className="form-check form-check-inline">
                <input
                    className="form-check-input ibexa-input ibexa-input--checkbox"
                    type="checkbox"
                    checked={isColumnVisible}
                    readOnly={true}
                />
                <label className="form-check-label c-table-view-columns-toggler-list-element__label">{label}</label>
            </div>
        </li>
    );
};

TableViewColumnsTogglerListElement.propTypes = {
    label: PropTypes.string.isRequired,
    columnKey: PropTypes.string.isRequired,
    isColumnVisible: PropTypes.bool.isRequired,
    toggleColumnVisibility: PropTypes.func.isRequired,
};

export default TableViewColumnsTogglerListElement;
