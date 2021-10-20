import React, { useState, useEffect, useRef, useContext } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import Icon from '../../../common/icon/icon';

import { DropdownPortalRefContext } from '../../universal.discovery.module';

const Dropdown = ({ value, options, onChange, small }) => {
    const containerRef = useRef();
    const containerItemsRef = useRef();
    const dropdownPortalRef = useContext(DropdownPortalRefContext);
    const [isExpanded, setIsExpanded] = useState(false);
    const [filterText, setFilterText] = useState('');
    const labelValue = options.find((option) => option.value === value)?.label;
    const dropdownClassName = createCssClassNames({
        'c-udw-dropdown ibexa-dropdown ibexa-dropdown--single': true,
        'ibexa-dropdown--small': small,
    });
    const toggleExpanded = () => {
        setIsExpanded((prevState) => !prevState);
    };
    const updateFilterValue = ({ target: { value } }) => setFilterText(value);
    const resetInputValue = () => setFilterText('');
    const showItem = (itemValue, searchedTerm) => {
        if (searchedTerm.length < 3) {
            return true;
        }

        const itemValueLowerCase = itemValue.toLowerCase();
        const searchedTermLowerCase = searchedTerm.toLowerCase();

        return itemValueLowerCase.indexOf(searchedTermLowerCase) === 0;
    }
    const renderItems = () => {
        const itemsStyles = {};
        const placeholder = Translator.trans(/*@Desc("Search...")*/ 'dropdown.placeholder', {}, 'universal_discovery_widget')

        if (containerRef.current) {
            const { width, left, top, height } = containerRef.current.getBoundingClientRect();

            itemsStyles.width = width;
            itemsStyles.left = left;
            itemsStyles.top = top + height + 8;
        }

        return (
            <div class="c-udw-dropdown__items ibexa-dropdown__items" style={itemsStyles} ref={containerItemsRef} >
                <div class="ibexa-input-text-wrapper">
                    <input
                        type="text"
                        placeholder={placeholder}
                        className="ibexa-dropdown__items-filter ibexa-input ibexa-input--small ibexa-input--text form-control"
                        onChange={updateFilterValue}
                        value={filterText}
                    />
                    <div class="ibexa-input-text-wrapper__actions">
                        <button
                            type="button"
                            class="btn ibexa-input-text-wrapper__action-btn ibexa-input-text-wrapper__action-btn--clear"
                            tabindex="-1"
                            onClick={resetInputValue}
                        >
                            <Icon name="discard" />
                        </button>
                        <button
                            type="button"
                            class="btn ibexa-input-text-wrapper__action-btn ibexa-input-text-wrapper__action-btn--search"
                            tabindex="-1"
                        >
                            <Icon name="search" extraClasses="ibexa-icon--small" />
                        </button>
                    </div>
                </div>
                <ul class="ibexa-dropdown__items-list">
                    {
                        options.map((option) => {
                            const optionClassName = createCssClassNames({
                                'ibexa-dropdown__item': true,
                                'ibexa-dropdown__item--selected': option.value === value,
                                'ibexa-dropdown__item--hidden': !showItem(option.label, filterText),
                            });

                            return (
                                <li class={optionClassName} key={option.value} onClick={() => onChange(option.value)}>
                                    <span class="ibexa-dropdown__item-label">{ option.label }</span>
                                </li>
                            )
                        })
                    }
                </ul>
            </div>
        )
    }

    useEffect(() => {
        if (!isExpanded) {
            return;
        }

        const onInteractionOutside = (event) => {
            if (containerRef.current.contains(event.target) || containerItemsRef.current.contains(event.target)) {
                return;
            }

            setIsExpanded(false);
        }

        document.body.addEventListener('click', onInteractionOutside, false);

        return () => {
            document.body.removeEventListener('click', onInteractionOutside, false);
        }
    }, [isExpanded]);

    useEffect(() => {
        setIsExpanded(false);
    }, [value]);

    return (
        <>
            <div
                className={dropdownClassName}
                ref={containerRef}
                onClick={toggleExpanded}
            >
                <div class="ibexa-dropdown__source">
                </div>
                <div class="ibexa-dropdown__wrapper">
                    <ul class="ibexa-dropdown__selection-info">
                        <li class="ibexa-dropdown__selected-item">
                            { labelValue }
                        </li>
                    </ul>
                </div>
            </div>
            {isExpanded && ReactDOM.createPortal(
                renderItems(),
                dropdownPortalRef.current,
            )}
        </>
    );
};

Dropdown.propTypes = {
    value: PropTypes.string.isRequired,
    options: PropTypes.array.isRequired,
    onChange: PropTypes.func.isRequired,
    small: PropTypes.bool,
};

Dropdown.defaultProps = {
    small: false,
};

export default Dropdown;
