import React, { useState, useEffect, useRef } from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import Icon from '../../../common/icon/icon';

const Dropdown = ({ options, selectedOption, onOptionClick, isDisabled }) => {
    const containerRef = useRef();
    const onInteractionOutsideRef = useRef
    const [isExpanded, setIsExpanded] = useState(false);
    const dropdownClass = createCssClassNames({
        'c-udw-dropdown': true,
        'c-udw-dropdown--is-expanded': isExpanded,
        'c-udw-dropdown--is-disabled': isDisabled,
    });
    const getCaretIcon = () => {
        const iconName = isExpanded ? 'caret-up' : 'caret-down';

        return <Icon name={iconName} extraClasses="ibexa-icon--tiny c-udw-dropdown__expand-icon" />
    }
    const toggleExpanded = () => {
        if (isDisabled) {
            return;
        }

        setIsExpanded(!isExpanded);
    };
    const onOptionClickWrapper = (option) => {
        onOptionClick(option);

        setIsExpanded(false);
    }
    const onInteractionOutside = (event) => {
        if (containerRef.current.contains(event.target)) {
            return;
        }

        setIsExpanded(false);
    }

    useEffect(() => {
        const bodyMethodName = isExpanded ? 'addEventListener' : 'removeEventListener';

        document.body[bodyMethodName]('click', onInteractionOutside, false);
    }, [isExpanded]);

    return (
        <div className={dropdownClass} ref={containerRef}>
            <div className="c-udw-dropdown__selected" onClick={toggleExpanded}>
                <span>{selectedOption.label}</span>
                {getCaretIcon()}
            </div>
            <div className="c-udw-dropdown__items">
                <ul className="c-udw-dropdown__list-items">
                    {options.map((option) => {
                        const isOptionSelected = option.id === selectedOption.id;
                        const optionClass = createCssClassNames({
                            'c-udw-dropdown__list-item': true,
                            'c-udw-dropdown__list-item--selected': isOptionSelected
                        });

                        return (
                            <li className={optionClass} onClick={onOptionClickWrapper.bind(null, option)}>
                                <span>{option.label}</span>
                                {isOptionSelected && <Icon name="checkmark" extraClasses="c-udw-dropdown__list-item-checkmark ibexa-icon--small" />}
                            </li>
                        );
                    })}
                </ul>
            </div>
        </div>
    );
};

Dropdown.propTypes = {
    options: PropTypes.array.isRequired,
    selectedOption: PropTypes.object.isRequired,
    onOptionClick: PropTypes.func.isRequired,
    isDisabled: PropTypes.bool,
};

Dropdown.defaultProps = {
    isDisabled: false,
};

export default Dropdown;
