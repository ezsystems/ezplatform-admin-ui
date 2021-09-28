import React, { useState } from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '../helpers/css.class.names';
import Icon from '../icon/icon';

const ENTER_CHAR_CODE = 13;

const InputText = ({ extraClasses, hasSearch, placeholder, search }) => {
    const [inputValue, setInputValue] = useState('');
    const className = createCssClassNames({
        'ibexa-input-text-wrapper': true,
        'ibexa-input-text-wrapper--search': hasSearch,
        [extraClasses]: extraClasses,
    });
    const updateInputValue = ({ target: { value } }) => setInputValue(value);
    const resetInputValue = () => setInputValue('');
    const searchWrapper = () => search(inputValue);
    const handleKeyPressed = ({ charCode }) => {
        if (hasSearch && charCode === ENTER_CHAR_CODE) {
            searchWrapper();
        }
    };

    return (
        <div class={className}>
            <input
                type="text"
                placeholder={placeholder}
                className="ibexa-dropdown__items-filter ibexa-input ibexa-input--text ibexa-input--small form-control"
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
                { hasSearch && (
                    <button
                        type="button"
                        class="btn ibexa-input-text-wrapper__action-btn ibexa-input-text-wrapper__action-btn--search"
                        tabindex="-1"
                        onClick={searchWrapper}
                    >
                        <Icon name="search" extraClasses="ibexa-icon--small" />
                    </button>
                )}
            </div>
        </div>
    );
};

InputText.propTypes = {
    extraClasses: PropTypes.string,
    hasSearch: PropTypes.bool,
    placeholder: PropTypes.string,
    search: PropTypes.func,
    small: PropTypes.bool,
};

InputText.defaultProps = {
    extraClasses: null,
    hasSearch: true,
    placeholder: Translator.trans(/*@Desc("Search...")*/ 'input.text.placeholder.default', {}, 'common'),
    search: () => {},
};

export default InputText;
