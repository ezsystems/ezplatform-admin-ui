import React from 'react';
import PropTypes from 'prop-types';
import Icon from '../../../common/icon/icon';

const GridViewItemComponent = ({ item, generateLink }) => {
    const { id: locationId, content } = item;
    const imageClassName = 'ibexa-grid-view-item__image';
    const contentTypeIdentifier = content._info.contentType.identifier;
    const contentTypeIconUrl = eZ.helpers.contentType.getContentTypeIconUrl(contentTypeIdentifier);
    let image = null;
    let contentTypeIcon = null;

    if (content._thumbnail === null || content._thumbnail.mimeType === 'image/svg+xml') {
        image = (
            <div className={`${imageClassName} ${imageClassName}--none`}>
                <Icon customPath={contentTypeIconUrl} extraClasses="ibexa-icon--extra-large" />
            </div>
        );
    } else {
        const { uri, alternativeText } = content._thumbnail;

        image = <img className={imageClassName} src={uri} alt={alternativeText} />;
        contentTypeIcon = (
            <div className="ibexa-grid-view-item__content-type">
                <Icon customPath={contentTypeIconUrl} extraClasses="ibexa-icon--small" />
            </div>
        );
    }

    return (
        <a className="ibexa-grid-view-item" href={generateLink(locationId, content._info.id)}>
            {contentTypeIcon}
            <div className="ibexa-grid-view-item__image-wrapper">{image}</div>
            <div className="ibexa-grid-view-item__title-wrapper">
                <div className="ibexa-grid-view-item__title" title={content._name}>
                    {content._name}
                </div>
            </div>
        </a>
    );
};

GridViewItemComponent.propTypes = {
    item: PropTypes.object.isRequired,
    generateLink: PropTypes.func.isRequired,
};

export default GridViewItemComponent;
