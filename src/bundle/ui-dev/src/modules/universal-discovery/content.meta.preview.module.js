import React, { useContext, useEffect, useMemo, useState, useRef } from 'react';

import Icon from '../common/icon/icon';
import Thumbnail from '../common/thumbnail/thumbnail';
import TranslationSelector from './components/translation-selector/translation.selector';
import ContentEditButton from './components/content-edit-button/content.edit.button';

import { addBookmark, removeBookmark } from './services/universal.discovery.service';
import {
    MarkedLocationIdContext,
    LoadedLocationsMapContext,
    ContentTypesMapContext,
    RestInfoContext,
    AllowRedirectsContext,
} from './universal.discovery.module';

export const getLocationData = (loadedLocationsMap, markedLocationId) =>
    loadedLocationsMap.find((loadedLocation) => loadedLocation.parentLocationId === markedLocationId) ||
    (loadedLocationsMap.length &&
        loadedLocationsMap[loadedLocationsMap.length - 1].subitems.find((subitem) => subitem.location.id === markedLocationId));

const ContentMetaPreview = () => {
    const refContentMetaPreview = useRef(null);
    const [markedLocationId, setMarkedLocationId] = useContext(MarkedLocationIdContext);
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useContext(LoadedLocationsMapContext);
    const contentTypesMap = useContext(ContentTypesMapContext);
    const restInfo = useContext(RestInfoContext);
    const allowRedirects = useContext(AllowRedirectsContext);
    const { formatShortDateTime } = window.eZ.helpers.timezone;
    const locationData = useMemo(() => getLocationData(loadedLocationsMap, markedLocationId), [markedLocationId, loadedLocationsMap]);

    const bookmarkLabel = Translator.trans(/*@Desc("Bookmark")*/ 'meta_preview.bookmark', {}, 'universal_discovery_widget');
    const previewLabel = Translator.trans(/*@Desc("Preview")*/ 'meta_preview.preview', {}, 'universal_discovery_widget');

    useEffect(() => {
        window.eZ.helpers.tooltips.parse(refContentMetaPreview.current);
    });

    if (!locationData || !locationData.location || !locationData.version || markedLocationId === 1) {
        return null;
    }

    const { bookmarked, location, version, permissions } = locationData;
    const bookmarkIconName = bookmarked ? 'bookmark-active' : 'bookmark';
    const toggleBookmarked = () => {
        const toggleBookmark = bookmarked ? removeBookmark : addBookmark;

        toggleBookmark({ ...restInfo, locationId: location.id }, () => {
            dispatchLoadedLocationsAction({ type: 'UPDATE_LOCATIONS', data: { ...locationData, bookmarked: !bookmarked } });
        });
    };
    const previewContent = () => {
        window.location.href = window.Routing.generate(
            '_ez_content_view',
            { contentId: location.ContentInfo.Content._id, locationId: location.id },
            true
        );
    };
    const renderActions = () => {
        const previewButton = allowRedirects ? (
            <button
                className="c-content-meta-preview__preview-button btn"
                onClick={previewContent}
                data-tooltip-container-selector=".c-udw-tab"
                title={previewLabel}>
                <Icon name="view" extraClasses="ez-icon--secondary" />
            </button>
        ) : null;
        const hasAccess = permissions && permissions.edit.hasAccess;

        return (
            <div className="c-content-meta-preview__actions">
                <ContentEditButton location={location} version={version} isDisabled={!hasAccess} />
                {previewButton}
            </div>
        );
    };
    const lastModifiedLabel = Translator.trans(/*@Desc("Last Modified")*/ 'meta_preview.last_modified', {}, 'universal_discovery_widget');
    const creationDateLabel = Translator.trans(/*@Desc("Creation Date")*/ 'meta_preview.creation_date', {}, 'universal_discovery_widget');
    const translationsLabel = Translator.trans(/*@Desc("Translations")*/ 'meta_preview.translations', {}, 'universal_discovery_widget');

    return (
        <div className="c-content-meta-preview" ref={refContentMetaPreview}>
            <div className="c-content-meta-preview__preview">
                <Thumbnail thumbnailData={version.Thumbnail} iconExtraClasses="ez-icon--extra-large" />
            </div>
            <div className="c-content-meta-preview__header">
                <span className="c-content-meta-preview__content-name">{location.ContentInfo.Content.TranslatedName}</span>
                <button
                    className="c-content-meta-preview__toggle-bookmark-button"
                    onClick={toggleBookmarked}
                    title={bookmarkLabel}
                    data-placement="left"
                    data-tooltip-container-selector=".c-content-meta-preview">
                    <Icon name={bookmarkIconName} extraClasses="ez-icon--small ez-icon--secondary" />
                </button>
            </div>
            {renderActions()}
            <div className="c-content-meta-preview__info">
                <div className="c-content-meta-preview__content-type-name">
                    {contentTypesMap[location.ContentInfo.Content.ContentType._href].name}
                </div>
                <div className="c-content-meta-preview__details">
                    <div className="c-content-meta-preview__details-item">
                        <span>{lastModifiedLabel}</span>
                        <span>{formatShortDateTime(new Date(location.ContentInfo.Content.lastModificationDate))}</span>
                    </div>
                    <div className="c-content-meta-preview__details-item">
                        <span>{creationDateLabel}</span>
                        <span>{formatShortDateTime(new Date(location.ContentInfo.Content.publishedDate))}</span>
                    </div>
                    <div className="c-content-meta-preview__details-item">
                        <span>{translationsLabel}</span>
                        <div className="c-content-meta-preview__translations-wrapper">
                            {version.VersionInfo.languageCodes.split(',').map((languageCode) => {
                                return (
                                    <span key={languageCode} className="c-content-meta-preview__translation">
                                        {window.eZ.adminUiConfig.languages.mappings[languageCode].name}
                                    </span>
                                );
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

eZ.addConfig('adminUiConfig.universalDiscoveryWidget.contentMetaPreview', ContentMetaPreview);

export default ContentMetaPreview;
