import React, { useState, useEffect, useRef } from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import Icon from '../../../common/icon/icon';

const SimpleDropdown = ({ options, selectedOption, onOptionClick, isDisabled }) => {
    const containerRef = useRef();
    const [isExpanded, setIsExpanded] = useState(false);
    const dropdownClass = createCssClassNames({
        'c-udw-simple-dropdown': true,
        'c-udw-simple-dropdown--expanded': isExpanded,
        'c-udw-simple-dropdown--disabled': isDisabled,
    });
    const getCaretIcon = () => {
        const iconName = isExpanded ? 'caret-up' : 'caret-down';

        return <Icon name={iconName} extraClasses="ibexa-icon--tiny c-udw-simple-dropdown__expand-icon" />
    }
    const toggleExpanded = () => {
        if (isDisabled) {
            return;
        }

        setIsExpanded((prevState) => !prevState);
    };
    const onOptionClickWrapper = (option) => {
        onOptionClick(option);

        setIsExpanded(false);
    }
    const renderItem = (item) => {
        const isItemSelected = item.id === selectedOption.id;
        const itemClass = createCssClassNames({
            'c-udw-simple-dropdown__list-item': true,
            'c-udw-simple-dropdown__list-item--selected': isItemSelected
        });

        return (
            <li className={itemClass} onClick={() => onOptionClickWrapper(item)}>
                <span>{item.label}</span>
                {isItemSelected && <Icon name="checkmark" extraClasses="c-udw-simple-dropdown__list-item-checkmark ibexa-icon--small" />}
            </li>
        );
    }

    useEffect(() => {
        if (!isExpanded) {
            return;
        }

        const onInteractionOutside = (event) => {
            if (containerRef.current.contains(event.target)) {
                return;
            }

            setIsExpanded(false);
        }

        document.body.addEventListener('click', onInteractionOutside, false);

        return () => {
            document.body.removeEventListener('click', onInteractionOutside, false);
        }
    }, [isExpanded]);

    return (
        <div className={dropdownClass} ref={containerRef}>
            <div className="c-udw-simple-dropdown__selected" onClick={toggleExpanded}>
                <span>{selectedOption.label}</span>
                {getCaretIcon()}
            </div>
            <div className="c-udw-simple-dropdown__items">
                <ul className="c-udw-simple-dropdown__list-items">
                    {options.map(renderItem)}
                </ul>
            </div>
        </div>
    );
};

SimpleDropdown.propTypes = {
    options: PropTypes.array.isRequired,
    selectedOption: PropTypes.object.isRequired,
    onOptionClick: PropTypes.func.isRequired,
    isDisabled: PropTypes.bool,
};

SimpleDropdown.defaultProps = {
    isDisabled: false,
};

export default SimpleDropdown;
