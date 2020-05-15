import React, { useContext, useState, useEffect, useRef } from 'react';

import Icon from '../../../common/icon/icon';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import {
    CreateContentWidgetContext,
    ActiveTabContext,
    ContentOnTheFlyDataContext,
    MarkedLocationIdContext,
    LoadedLocationsMapContext,
    ContentOnTheFlyConfigContext,
    AllowedContentTypesContext,
} from '../../universal.discovery.module';

const languages = Object.values(window.eZ.adminUiConfig.languages.mappings);
const contentTypes = Object.entries(window.eZ.adminUiConfig.contentTypes);

const ContentCreateWidget = () => {
    const refContentTree = useRef(null);
    const [markedLocationId, setMarkedLocationId] = useContext(MarkedLocationIdContext);
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useContext(LoadedLocationsMapContext);
    const { allowedLanguages, preselectedLanguage, preselectedContentType } = useContext(ContentOnTheFlyConfigContext);
    const allowedContentTypes = useContext(AllowedContentTypesContext);
    const selectedLocation = loadedLocationsMap.find((loadedLocation) => loadedLocation.parentLocationId === markedLocationId);
    const filteredLanguages = languages.filter((language) => {
        const userHasPermission =
            !selectedLocation ||
            !selectedLocation.permissions ||
            !selectedLocation.permissions.create.restrictedLanguageCodes.length ||
            selectedLocation.permissions.create.restrictedLanguageCodes.includes(language.languageCode);
        const isAllowedLanguage = !allowedLanguages || allowedLanguages.includes(language.languageCode);

        return userHasPermission && isAllowedLanguage;
    });
    const [filterQuery, setFilterQuery] = useState('');
    const firstLanguageCode = filteredLanguages.length ? filteredLanguages[0].languageCode : '';
    const [selectedLanguage, setSelectedLanguage] = useState(preselectedLanguage || firstLanguageCode);
    const [selectedContentType, setSelectedContentType] = useState(preselectedContentType);
    const [activeTab, setActiveTab] = useContext(ActiveTabContext);
    const [createContentVisible, setCreateContentVisible] = useContext(CreateContentWidgetContext);
    const [contentOnTheFlyData, setContentOnTheFlyData] = useContext(ContentOnTheFlyDataContext);
    const close = () => {
        setCreateContentVisible(false);
    };
    const updateFilterQuery = (event) => {
        const query = event.target.value.toLowerCase();

        setFilterQuery(query);
    };
    const updateSelectedLanguage = (event) => setSelectedLanguage(event.target.value);
    const isConfirmDisabled = !selectedContentType || !selectedLanguage || markedLocationId === 1;
    const createContent = () => {
        if (window.parent) {
            window.parent.document.body.dispatchEvent(new CustomEvent('ez-udw-hide-footer'));
        }

        setContentOnTheFlyData({
            locationId: markedLocationId,
            languageCode: selectedLanguage,
            contentTypeIdentifier: selectedContentType,
        });
        setActiveTab('content-create');
    };
    const createContentLabel = Translator.trans(/*@Desc("Create new content")*/ 'create_content.label', {}, 'universal_discovery_widget');
    const selectLanguageLabel = Translator.trans(
        /*@Desc("Select a language")*/ 'create_content.select_language',
        {},
        'universal_discovery_widget'
    );
    const selectContentType = Translator.trans(
        /*@Desc("Select a Content Type")*/ 'create_content.select_content_type',
        {},
        'universal_discovery_widget'
    );
    const createLabel = Translator.trans(/*@Desc("Create")*/ 'create_content.create', {}, 'universal_discovery_widget');
    const closeLabel = Translator.trans(/*@Desc("Close")*/ 'popup.close.label', {}, 'universal_discovery_widget');
    const widgetClassName = createCssClassNames({
        'c-content-create': true,
        'c-content-create--hidden': !createContentVisible,
    });

    useEffect(() => {
        window.eZ.helpers.tooltips.parse(refContentTree.current);
    }, []);

    return (
        <div className={widgetClassName} ref={refContentTree}>
            <div className="c-content-create__header">
                <div className="c-content-create__header-title">{createContentLabel}</div>
                <button
                    type="button"
                    className="c-content-create__close-button"
                    onClick={close}
                    title={closeLabel}
                    data-tooltip-container-selector=".c-udw-tab">
                    <Icon name="discard" extraClasses="ez-icon--small" />
                </button>
            </div>
            <div className="c-content-create__language-selector-wrapper">
                <div className="c-content-create__language-selector-label">{selectLanguageLabel}</div>
                <select className="form-control" onChange={updateSelectedLanguage} value={selectedLanguage}>
                    {filteredLanguages.map((language) => {
                        if (!language.enabled) {
                            return null;
                        }

                        return (
                            <option key={language.id} value={language.languageCode} onChange={updateSelectedLanguage}>
                                {language.name}
                            </option>
                        );
                    })}
                </select>
            </div>
            <div className="c-content-create__select-content-type-wrapper">
                <div className="c-content-create__select-content-type-label">{selectContentType}</div>
                <input autoFocus className="form-control" type="text" placeholder="Type to refine" onChange={updateFilterQuery} />
                <div className="c-content-create__content-type-list">
                    {contentTypes.map(([groupName, groupItems]) => {
                        const isHidden = groupItems.every((groupItem) => {
                            return (
                                (filterQuery && !groupItem.name.toLowerCase().includes(filterQuery)) ||
                                (selectedLocation &&
                                    selectedLocation.permissions &&
                                    selectedLocation.permissions.create.restrictedContentTypeIds.length &&
                                    !selectedLocation.permissions.create.restrictedContentTypeIds.includes(groupItem.id.toString())) ||
                                (allowedContentTypes && !allowedContentTypes.includes(groupItem.identifier))
                            );
                        });

                        return (
                            <div className="c-content-create__group" key={groupName}>
                                <div className="c-content-create__group-name" hidden={isHidden}>
                                    {groupName}
                                </div>
                                {groupItems.map(({ name, thumbnail, identifier, id }) => {
                                    const isHidden =
                                        (filterQuery && !name.toLowerCase().includes(filterQuery)) ||
                                        (selectedLocation &&
                                            selectedLocation.permissions &&
                                            selectedLocation.permissions.create.restrictedContentTypeIds.length &&
                                            !selectedLocation.permissions.create.restrictedContentTypeIds.includes(id.toString())) ||
                                        (allowedContentTypes && !allowedContentTypes.includes(identifier));
                                    const className = createCssClassNames({
                                        'c-content-create__group-item': true,
                                        'c-content-create__group-item--selected': identifier === selectedContentType,
                                    });
                                    const updateSelectedContentType = () => setSelectedContentType(identifier);

                                    return (
                                        <div hidden={isHidden} key={identifier} className={className} onClick={updateSelectedContentType}>
                                            <div className="c-content-create__group-item-icon">
                                                <Icon customPath={thumbnail} extraClasses="ez-icon--small" />
                                            </div>
                                            <div className="c-content-create__group-item-name">{name}</div>
                                        </div>
                                    );
                                })}
                            </div>
                        );
                    })}
                </div>
            </div>
            <div className="c-content-create__confirm-wrapper">
                <button className="c-content-create__confirm-button btn btn-primary" onClick={createContent} disabled={isConfirmDisabled}>
                    {createLabel}
                </button>
            </div>
        </div>
    );
};

export default ContentCreateWidget;
