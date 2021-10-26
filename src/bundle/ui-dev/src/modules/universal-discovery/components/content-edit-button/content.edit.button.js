import React, { useState, useEffect, useContext } from 'react';
import PropTypes from 'prop-types';

import Icon from '../../../common/icon/icon';
import { createCssClassNames } from '../../../common/helpers/css.class.names';
import TranslationSelector from '../translation-selector/translation.selector';
import { createDraft } from '../..//services/universal.discovery.service';
import {
    RestInfoContext,
    EditOnTheFlyDataContext,
    AllowRedirectsContext,
    ActiveTabContext,
    ContentTypesMapContext,
} from '../..//universal.discovery.module';

const ContentEditButton = ({ version, location, isDisabled, label }) => {
    const restInfo = useContext(RestInfoContext);
    const allowRedirects = useContext(AllowRedirectsContext);
    const [editOnTheFlyData, setEditOnTheFlyData] = useContext(EditOnTheFlyDataContext);
    const [activeTab, setActiveTab] = useContext(ActiveTabContext);
    const contentTypesMap = useContext(ContentTypesMapContext);
    const [isTranslationSelectorVisible, setIsTranslationSelectorVisible] = useState(false);
    const contentTypeInfo = contentTypesMap[location.ContentInfo.Content.ContentType._href];
    const isUserContentType = window.eZ.adminUiConfig.userContentTypes.includes(contentTypeInfo.identifier);
    const btnClassName = createCssClassNames({
        'c-content-edit-button__btn btn ibexa-btn ibexa-btn--ghost': true,
        'ibexa-btn--no-text': label !== null,
    });

    useEffect(() => {
        setIsTranslationSelectorVisible(false);
    }, [version]);

    const hideTranslationSelector = () => {
        setIsTranslationSelectorVisible(false);
    };
    const toggleTranslationSelectorVisibility = () => {
        const languageCodes = version.VersionInfo.languageCodes.split(',');

        if (languageCodes.length === 1) {
            editContent(languageCodes[0]);
        } else {
            setIsTranslationSelectorVisible(true);
        }
    };
    const redirectToContentEdit = (contentId, versionNo, language, locationId) => {
        if (allowRedirects) {
            const href = isUserContentType
                ? window.Routing.generate(
                    'ezplatform.user.update',
                    {
                        contentId,
                        versionNo,
                        language,
                    },
                    true
                )
                : window.Routing.generate(
                    'ezplatform.content.draft.edit',
                    {
                        contentId,
                        versionNo,
                        language,
                        locationId,
                    },
                    true
                );

            window.location.href = href;

            return;
        }

        setEditOnTheFlyData({
            contentId,
            versionNo,
            languageCode: language,
            locationId,
        });
        setActiveTab('content-edit');
    };
    const editContent = (languageCode) => {
        const contentId = location.ContentInfo.Content._id;

        if (isUserContentType) {
            redirectToContentEdit(contentId, version.VersionInfo.versionNo, languageCode, location.id);

            return;
        }

        createDraft(
            {
                ...restInfo,
                contentId,
            },
            (response) => redirectToContentEdit(contentId, response.Version.VersionInfo.versionNo, languageCode, location.id)
        );
    };
    const renderTranslationSelector = () => {
        return (
            <TranslationSelector
                hideTranslationSelector={hideTranslationSelector}
                selectTranslation={editContent}
                version={version}
                isOpen={isTranslationSelectorVisible && version}
            />
        );
    };

    return (
        <div className="c-content-edit-button">
            <button
                className={btnClassName}
                disabled={!version || isDisabled}
                onClick={toggleTranslationSelectorVisibility}
                data-tooltip-container-selector=".c-udw-tab"
            >
                <Icon name="edit" extraClasses="ibexa-icon--small" />
                {label}
            </button>
            {renderTranslationSelector()}
        </div>
    );
};

ContentEditButton.propTypes = {
    location: PropTypes.object.isRequired,
    version: PropTypes.object.isRequired,
    isDisabled: PropTypes.bool.isRequired,
    label: PropTypes.node,
};

ContentEditButton.defaultProps = {
    label: null,
}

export default ContentEditButton;
