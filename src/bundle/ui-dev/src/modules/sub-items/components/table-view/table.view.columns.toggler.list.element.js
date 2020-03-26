import React from 'react';
import PropTypes from 'prop-types';

const TableViewColumnsTogglerListElement = ({ label, isColumnVisible, toggleColumnVisibility, columnKey }) => {
    return (
        <li className="c-table-view-columns-toggler-list-element" onClick={() => toggleColumnVisibility(columnKey)}>
            <div className="form-check form-check-inline">
                <input className="form-check-input" type="checkbox" checked={isColumnVisible} readOnly={true} />
                <label className="c-table-view-columns-toggler-list-element__label form-check-label">{label}</label>
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
