import React from 'react';
import PropTypes from 'prop-types';
import Icon from '../../../common/icon/icon';

const ViewSwitcherButton = ({ id, icon, title, onClick, activeView, isDisabled }) => {
    const baseClassName = 'c-view-switcher-btn';
    const attrs = {
        id,
        onClick: () => onClick(id),
        className: baseClassName,
        title,
        tabIndex: '-1',
    };
    const iconAttrs = {
        name: icon,
        extraClasses: 'ibexa-icon--base-dark ibexa-icon--small',
    };

    if (activeView === id) {
        attrs.className = `${baseClassName} ${baseClassName}--active`;
        iconAttrs.extraClasses = 'ibexa-icon--light ibexa-icon--small';
    }

    if (isDisabled) {
        attrs.className = `${attrs.className} ${baseClassName}--disabled`;
    }

    return (
        <div {...attrs}>
            <Icon {...iconAttrs} />
        </div>
    );
};

ViewSwitcherButton.propTypes = {
    id: PropTypes.string.isRequired,
    activeView: PropTypes.string.isRequired,
    isDisabled: PropTypes.bool.isRequired,
    icon: PropTypes.string.isRequired,
    title: PropTypes.string.isRequired,
    onClick: PropTypes.func.isRequired,
};

export default ViewSwitcherButton;
