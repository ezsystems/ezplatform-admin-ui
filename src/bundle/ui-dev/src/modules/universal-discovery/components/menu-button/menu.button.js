import React from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '../../../common/helpers/css.class.names';

const MenuButton = ({ extraClasses, onClick, isDisabled, title, children }) => {
    const className = createCssClassNames({
        'c-menu-button': true,
        [extraClasses]: !!extraClasses,
    });

    return (
        <button className={className} onClick={onClick} disabled={isDisabled} title={title} data-tooltip-container-selector=".c-udw-tab">
            {children}
        </button>
    );
};

MenuButton.propTypes = {
    extraClasses: PropTypes.string,
    onClick: PropTypes.func.isRequired,
    isDisabled: PropTypes.bool,
    title: PropTypes.string,
    children: PropTypes.any,
};

MenuButton.defaultProps = {
    children: [],
    extraClasses: '',
    isDisabled: false,
    title: '',
};

export default MenuButton;
