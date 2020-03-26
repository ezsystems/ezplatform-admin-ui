import React from 'react';
import PropTypes from 'prop-types';

const ThreeStateCheckboxComponent = ({ indeterminate, ...restOfProps }) => (
    <input
        {...restOfProps}
        type="checkbox"
        ref={(input) => {
            if (input) {
                input.indeterminate = indeterminate;
            }
        }}
    />
);

ThreeStateCheckboxComponent.propTypes = {
    indeterminate: PropTypes.bool,
};

export default ThreeStateCheckboxComponent;
