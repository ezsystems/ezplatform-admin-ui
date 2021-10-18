import React from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import Icon from '../../../common/icon/icon';

const TranslationSelectorButton = ({ hideTranslationSelector, selectTranslation, version, isOpen }) => {
    const languageCodes = version ? version.VersionInfo.languageCodes.split(',') : [];
    const editTranslationLabel = Translator.trans(
        /*@Desc("Edit translation")*/ 'meta_preview.edit_translation',
        {},
        'universal_discovery_widget'
    );
    const className = createCssClassNames({
        'c-translation-selector': true,
        'c-translation-selector--hidden': !isOpen,
    });

    return (
        <div className={className}>
            <div className="c-translation-selector__header">
                <span className="c-translation-selector__title">{`${editTranslationLabel} (${languageCodes.length})`}</span>
                <button className="c-translation-selector__close-button btn" onClick={hideTranslationSelector}>
                    <Icon name="discard" extraClasses="ibexa-icon--small" />
                </button>
            </div>
            <div className="c-translation-selector__languages-wrapper">
                {languageCodes.map((languageCode) => (
                    <div
                        key={languageCode}
                        className="c-translation-selector__language"
                        onClick={selectTranslation.bind(this, languageCode)}>
                        {window.eZ.adminUiConfig.languages.mappings[languageCode].name}
                    </div>
                ))}
            </div>
        </div>
    );
};

TranslationSelectorButton.propTypes = {
    hideTranslationSelector: PropTypes.func.isRequired,
    selectTranslation: PropTypes.func.isRequired,
    version: PropTypes.object.isRequired,
    isOpen: PropTypes.bool.isRequired,
};

export default TranslationSelectorButton;
