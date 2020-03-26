import React from 'react';
import PropTypes from 'prop-types';
import Icon from '../../../common/icon/icon';

const GridViewItemComponent = ({ item, generateLink }) => {
    const { id: locationId, content } = item;
    const imageClassName = 'c-grid-view-item__image';
    const contentTypeIdentifier = content._info.contentType.identifier;
    const contentTypeIconUrl = eZ.helpers.contentType.getContentTypeIconUrl(contentTypeIdentifier);
    let image = null;
    let contentTypeIcon = null;

    if (content._thumbnail) {
        const { uri, alternativeText } = content._thumbnail;

        image = <img className={imageClassName} src={uri} alt={alternativeText} />;
        contentTypeIcon = (
            <div className="c-grid-view-item__content-type">
                <Icon customPath={contentTypeIconUrl} extraClasses="ez-icon--small" />
            </div>
        );
    } else {
        image = (
            <div className={`${imageClassName} ${imageClassName}--none`}>
                <Icon customPath={contentTypeIconUrl} extraClasses="ez-icon--extra-large" />
            </div>
        );
    }

    return (
        <a className="c-grid-view-item" href={generateLink(locationId, content._info.id)}>
            {contentTypeIcon}
            <div className="c-grid-view-item__image-wrapper">{image}</div>
            <div className="c-grid-view-item__title">{content._name}</div>
        </a>
    );
};

GridViewItemComponent.propTypes = {
    item: PropTypes.object.isRequired,
    generateLink: PropTypes.func.isRequired,
};

export default GridViewItemComponent;
