import React, { useContext, Fragment } from 'react';
import PropTypes from 'prop-types';

import ToggleSelection from '../toggle-selection/toggle.selection';
import Icon from '../../../common/icon/icon';
import Thumbnail from '../../../common/thumbnail/thumbnail';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import {
    LoadedLocationsMapContext,
    MarkedLocationIdContext,
    ContentTypesMapContext,
    SelectedLocationsContext,
    MultipleConfigContext,
    ContainersOnlyContext,
    AllowedContentTypesContext,
} from '../../universal.discovery.module';

const isSelectionButtonClicked = (event) => {
    return event.target.closest('.c-toggle-selection-button');
};

const GridViewItem = ({ location, version }) => {
    const [markedLocationId, setMarkedLocationId] = useContext(MarkedLocationIdContext);
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useContext(LoadedLocationsMapContext);
    const contentTypesMap = useContext(ContentTypesMapContext);
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const [multiple, multipleItemsLimit] = useContext(MultipleConfigContext);
    const containersOnly = useContext(ContainersOnlyContext);
    const allowedContentTypes = useContext(AllowedContentTypesContext);
    const contentTypeInfo = contentTypesMap[location.ContentInfo.Content.ContentType._href];
    const isContainer = contentTypeInfo.isContainer;
    const isNotSelectable =
        (containersOnly && !isContainer) || (allowedContentTypes && !allowedContentTypes.includes(contentTypeInfo.identifier));
    const className = createCssClassNames({
        'ibexa-grid-view-item': true,
        'ibexa-grid-view-item--marked': markedLocationId === location.id,
        'ibexa-grid-view-item--not-selectable': isNotSelectable,
    });
    const markLocation = ({ nativeEvent }) => {
        if (isSelectionButtonClicked(nativeEvent)) {
            return;
        }

        setMarkedLocationId(location.id);

        if (!multiple && !isNotSelectable) {
            dispatchSelectedLocationsAction({ type: 'CLEAR_SELECTED_LOCATIONS' });
            dispatchSelectedLocationsAction({ type: 'ADD_SELECTED_LOCATION', location });
        }
    };
    const loadLocation = ({ nativeEvent }) => {
        if (isSelectionButtonClicked(nativeEvent) || (containersOnly && !isContainer)) {
            return;
        }

        dispatchLoadedLocationsAction({ type: 'UPDATE_LOCATIONS', data: { parentLocationId: location.id, subitems: [] } });
    };
    const renderToggleSelection = () => {
        return (
            <div class="ibexa-grid-view-item__checkbox">
                <ToggleSelection location={location} multiple={multiple} isHidden={isNotSelectable} />
            </div>
        );
    };

    return (
        <div className={className} onClick={markLocation} onDoubleClick={loadLocation}>
            <div className="ibexa-grid-view-item__image-wrapper">
                <Thumbnail
                    thumbnailData={version.Thumbnail}
                    iconExtraClasses="ibexa-icon--extra-large"
                    contentTypeIconPath={contentTypesMap[location.ContentInfo.Content.ContentType._href].thumbnail}
                />
            </div>
            <div className="ibexa-grid-view-item__title-wrapper">
                <div className="ibexa-grid-view-item__title">{location.ContentInfo.Content.TranslatedName}</div>
            </div>
            {renderToggleSelection()}
        </div>
    );
};

GridViewItem.propTypes = {
    location: PropTypes.object.isRequired,
    version: PropTypes.object,
};

GridViewItem.defaultProps = {
    version: {},
};

export default GridViewItem;
