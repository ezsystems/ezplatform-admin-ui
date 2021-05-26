import React from 'react';
import PropTypes from 'prop-types';

const Icon = (props) => {
    const linkHref = props.customPath ? props.customPath : window.eZ.helpers.icon.getIconPath(props.name);
    let className = 'ibexa-icon';

    if (props.extraClasses) {
        className = `${className} ${props.extraClasses}`;
    }

    return (
        <svg className={className}>
            <use xlinkHref={linkHref} />
        </svg>
    );
};

Icon.propTypes = {
    extraClasses: PropTypes.string,
    name: PropTypes.string,
    customPath: PropTypes.string,
};

Icon.defaultProps = {
    customPath: null,
    name: null,
    extraClasses: null,
};

export default Icon;
