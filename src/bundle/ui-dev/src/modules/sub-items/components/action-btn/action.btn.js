import React from 'react';
import PropTypes from 'prop-types';
import Icon from '../../../common/icon/icon';
import { createCssClassNames } from '../../../common/helpers/css.class.names';

const ActionButton = (props) => {
    const { disabled, onClick, label, title, type } = props;
    const handleClick = () => {
        if (!disabled) {
            onClick();
        }
    };
    const className = createCssClassNames({
        'c-action-btn': true,
        'btn ibexa-btn': true,
        'ibexa-btn--ghost': true,
        'ibexa-btn--no-text': !label,
        [`c-action-btn--${type}`]: !!type,
    });

    return (
        <button type="button" className={className} title={title} onClick={handleClick} disabled={disabled}>
            <Icon name={type} extraClasses="ibexa-icon--small" /> {label}
        </button>
    );
};

ActionButton.propTypes = {
    label: PropTypes.string,
    title: PropTypes.string,
    disabled: PropTypes.bool.isRequired,
    type: PropTypes.string.isRequired,
    onClick: PropTypes.func.isRequired,
};

ActionButton.defaultPropTypes = {
    label: null,
    title: null,
};

export default ActionButton;
