import React from 'react';
import PropTypes from 'prop-types';
import Icon from '../icon/icon';
import { createCssClassNames } from '../helpers/css.class.names';

const ICON_NAME_MAP = {
    info: 'system-information',
    error: 'circle-close',
    warning: 'warning-triangle',
    success: 'checkbox',
};

const Alert = ({
    type,
    title,
    subtitle,
    iconName: iconNameProp,
    iconPath,
    showSubtitleBelow,
    showCloseBtn,
    onClose,
    extraClasses,
    children,
}) => {
    const className = createCssClassNames({
        'alert ibexa-alert': true,
        [`ibexa-alert--${type}`]: true,
        [extraClasses]: true,
    });
    const contentClassName = createCssClassNames({
        'ibexa-alert__content': true,
        'ibexa-alert__content--subtitle-below': showSubtitleBelow,
    });

    let iconName = undefined;

    if (!iconPath) {
        iconName = iconNameProp ? iconNameProp : ICON_NAME_MAP[type];
    }

    return (
        <div className={className} role="alert">
            <Icon name={iconName} customPath={iconPath} extraClasses="ibexa-icon--small ibexa-alert__icon" />
            <div className={contentClassName}>
                {title && <div className="ibexa-alert__title">{title}</div>}
                {subtitle && <div className="ibexa-alert__subtitle">{subtitle}</div>}
                <div className="ibexa-alert__extra_content">{children}</div>
            </div>
            {showCloseBtn && (
                <button
                    className="btn ibexa-btn ibexa-btn--ghost ibexa-btn--small ibexa-btn--no-text ibexa-alert__close-btn"
                    onClick={onClose}>
                    <Icon name="discard" extraClasses="ibexa-icon--tiny-small" />
                </button>
            )}
        </div>
    );
};

Icon.propTypes = {
    type: PropTypes.string.isRequired,
    title: PropTypes.string,
    subtitle: PropTypes.string,
    iconName: PropTypes.string,
    iconPath: PropTypes.string,
    showSubtitleBelow: PropTypes.bool,
    showCloseBtn: PropTypes.bool,
    onClose: PropTypes.func,
    extraClasses: PropTypes.string,
    children: PropTypes.element,
};

Icon.defaultProps = {
    title: null,
    subtitle: null,
    iconName: null,
    iconPath: null,
    showSubtitleBelow: false,
    showCloseBtn: false,
    onClose: () => {},
    extraClasses: '',
    children: null,
};

export default Alert;
