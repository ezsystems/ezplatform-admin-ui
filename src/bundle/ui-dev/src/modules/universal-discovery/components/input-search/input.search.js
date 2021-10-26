import React, { useState, useContext, useEffect, forwardRef } from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import Icon from '../../../common/icon/icon';

import { ActiveTabContext, SearchTextContext } from '../../universal.discovery.module';

const ENTER_CHAR_CODE = 13;
const SEARCH_TAB_ID = 'search';

const InputSearch = forwardRef(({ extraClasses, placeholder, search, small }, searchActionRef) => {
    const [activeTab, setActiveTab] = useContext(ActiveTabContext);
    const [searchText, setSearchText] = useContext(SearchTextContext);
    const [inputValue, setInputValue] = useState(searchText);
    const className = createCssClassNames({
        'ibexa-input-text-wrapper': true,
        'ibexa-input-text-wrapper--search': true,
        [extraClasses]: true,
    });
    const inputClassName = createCssClassNames({
        'ibexa-dropdown__items-filter ibexa-input ibexa-input--text form-control': true,
        'ibexa-input--small': small,
    });
    const updateInputValue = ({ target: { value } }) => setInputValue(value);
    const resetInputValue = () => setInputValue('');
    const searchWrapper = () => {
        if (search) {
            search(inputValue);
        } else {
            if (activeTab !== SEARCH_TAB_ID) {
                setActiveTab('search');
            }

            setSearchText(inputValue);
        }
    };
    const handleKeyPressed = ({ charCode }) => {
        if (charCode === ENTER_CHAR_CODE) {
            searchWrapper();
        }
    };

    if (searchActionRef) {
        searchActionRef.current = searchWrapper;
    }

    useEffect(() => {
        setInputValue(searchText);
    }, [searchText]);

    return (
        <div class={className}>
            <input
                type="text"
                placeholder={placeholder}
                className={inputClassName}
                onChange={updateInputValue}
                onKeyPress={handleKeyPressed}
                value={inputValue}
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
                    onClick={searchWrapper}
                >
                    <Icon name="search" extraClasses="ibexa-icon--small" />
                </button>
            </div>
        </div>
    );
});

InputSearch.displayName = 'InputSearch';

InputSearch.propTypes = {
    extraClasses: PropTypes.string,
    placeholder: PropTypes.string,
    search: PropTypes.func,
    small: PropTypes.bool,
};

InputSearch.defaultProps = {
    extraClasses: '',
    placeholder: Translator.trans(/*@Desc("Search...")*/ 'input.search.placeholder.default', {}, 'universal_discovery_widget'),
    search: null,
    small: true,
};

export default InputSearch;
