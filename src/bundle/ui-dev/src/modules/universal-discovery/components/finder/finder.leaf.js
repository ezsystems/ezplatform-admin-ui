import React, { useContext, useEffect } from 'react';
import PropTypes from 'prop-types';

import ToggleSelectionButton from '../toggle-selection-button/toggle.selection.button';
import Icon from '../../../common/icon/icon';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import {
    MarkedLocationIdContext,
    LoadedLocationsMapContext,
    ContentTypesMapContext,
    SelectedLocationsContext,
    MultipleConfigContext,
    ContainersOnlyContext,
    AllowedContentTypesContext,
} from '../../universal.discovery.module';

const FinderLeaf = ({ location }) => {
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
    const markLocation = ({ nativeEvent }) => {
        const isSelectionButtonClicked = nativeEvent.target.closest('.c-toggle-selection-button');
        const isMarkedLocationClicked = location.id === markedLocationId;

        if (isSelectionButtonClicked || isMarkedLocationClicked) {
            return;
        }

        setMarkedLocationId(location.id);
        dispatchLoadedLocationsAction({ type: 'CUT_LOCATIONS', locationId: markedLocationId });
        dispatchLoadedLocationsAction({ type: 'UPDATE_LOCATIONS', data: { parentLocationId: location.id, subitems: [] } });

        if (!multiple && !isNotSelectable) {
            dispatchSelectedLocationsAction({ type: 'CLEAR_SELECTED_LOCATIONS' });
            dispatchSelectedLocationsAction({ type: 'ADD_SELECTED_LOCATION', location });
        }
    };
    const renderToggleSelectionButton = () => {
        if (!multiple || isNotSelectable) {
            return null;
        }

        return <ToggleSelectionButton location={location} />;
    };
    const className = createCssClassNames({
        'c-finder-leaf': true,
        'c-finder-leaf--marked': !!loadedLocationsMap.find((loadedLocation) => loadedLocation.parentLocationId === location.id),
        'c-finder-leaf--has-children': !!location.childCount,
        'c-finder-leaf--not-selectable': isNotSelectable,
    });

    useEffect(() => {
        window.eZ.helpers.tooltips.parse(window.document.querySelector('.c-udw-tab'));
    }, []);

    return (
        <div className={className} onClick={markLocation}>
            <span className="c-finder-leaf__name">
                <span className="c-finder-leaf__icon-wrapper">
                    <Icon
                        extraClasses="ez-icon--small"
                        customPath={contentTypesMap[location.ContentInfo.Content.ContentType._href].thumbnail}
                    />
                </span>
                <span title={location.ContentInfo.Content.TranslatedName} data-tooltip-container-selector=".c-udw-tab">
                    {location.ContentInfo.Content.TranslatedName}
                </span>
            </span>
            {renderToggleSelectionButton()}
        </div>
    );
};

FinderLeaf.propTypes = {
    location: PropTypes.object.isRequired,
};

export default FinderLeaf;
