import React from 'react';
import PropTypes from 'prop-types';
import Icon from '../../../common/icon/icon';
import { createCssClassNames } from '../../../common/helpers/css.class.names';

const ActionButton = (props) => {
    const { disabled, onClick, label, type } = props;
    const baseClassName = 'c-action-btn';
    const handleClick = () => {
        if (!disabled) {
            onClick();
        }
    };
    const attrs = {
        type: 'button',
        title: label,
        tabIndex: '-1',
        onClick: handleClick,
    };

    attrs.disabled = disabled ? 'disabled' : false;
    attrs.className = createCssClassNames({
        [baseClassName]: true,
        [`${baseClassName}--disabled`]: disabled,
        [`${baseClassName}--${type}`]: !!type,
    });

    return (
        <button {...attrs}>
            <Icon name={type} extraClasses="ez-icon--base-dark ez-icon--medium" />
        </button>
    );
};

ActionButton.propTypes = {
    label: PropTypes.string.isRequired,
    disabled: PropTypes.bool.isRequired,
    type: PropTypes.string.isRequired,
    onClick: PropTypes.func.isRequired,
};

export default ActionButton;
