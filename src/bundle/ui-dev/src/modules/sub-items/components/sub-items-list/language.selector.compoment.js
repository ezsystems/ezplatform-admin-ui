import React, { useEffect } from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import InstantFilter from '../sub-items-list/instant.filter.component';

const LanguageSelector = (props) => {
    const className = createCssClassNames({
        'ibexa-extra-actions': true,
        'c-language-selector': true,
        'ibexa-extra-actions--edit': true,
        'ibexa-extra-actions--hidden': !props.isOpen,
    });
    const closeLanguageSelector = (event) => {
        if (!event.target.closest('.c-table-view-item__btn') && !event.target.classList.contains('ez-instant-filter__input')) {
            props.close();
        }
    };

    useEffect(() => {
        window.document.addEventListener('click', closeLanguageSelector, false);

        return () => {
            window.document.removeEventListener('click', closeLanguageSelector);
        };
    }, []);

    return (
        <div className={className}>
            <div className="ibexa-extra-actions__header">{props.label}</div>
            <div className="ibexa-extra-actions__content">
                <InstantFilter items={props.languageItems} handleItemChange={props.handleItemChange} />
            </div>
        </div>
    );
};

LanguageSelector.propTypes = {
    isOpen: PropTypes.bool,
    label: PropTypes.string,
    languageItems: PropTypes.array,
    handleItemChange: PropTypes.func,
    closeLanguageSelector: PropTypes.func,
};

LanguageSelector.defaultProps = {
    isOpen: false,
    label: '',
    languageItems: [],
    handleItemChange: () => {},
    closeLanguageSelector: () => {},
};

export default LanguageSelector;
