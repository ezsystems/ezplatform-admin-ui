import React from 'react';
import PropTypes from 'prop-types';

import Icon from '../icon/icon';

const Thumbnail = ({ thumbnailData, iconExtraClasses, contentTypeIconPath }) => {
    const renderContentTypeIcon = () => {
        if (!contentTypeIconPath) {
            return null;
        }

        return (
            <div className="c-thumbnail__icon-wrapper">
                <Icon extraClasses="ibexa-icon--small" customPath={contentTypeIconPath} />
            </div>
        );
    };

    if (thumbnailData.mimeType === 'image/svg+xml') {
        return (
            <div className="c-thumbnail">
                <Icon extraClasses={iconExtraClasses} customPath={thumbnailData.resource} />
            </div>
        );
    }

    return (
        <div className="c-thumbnail">
            {renderContentTypeIcon()}
            <img src={thumbnailData.resource} />
        </div>
    );
};

Thumbnail.propTypes = {
    thumbnailData: PropTypes.shape({
        mimeType: PropTypes.string.isRequired,
        resource: PropTypes.string.isRequired,
    }).isRequired,
    iconExtraClasses: PropTypes.string,
    contentTypeIconPath: PropTypes.string,
};

Thumbnail.defaultProps = {
    iconExtraClasses: null,
    contentTypeIconPath: null,
};

export default Thumbnail;
