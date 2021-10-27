import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import ToggleSelection from '../toggle-selection/toggle.selection';
import Icon from '../../../common/icon/icon';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import { loadAccordionData } from '../../services/universal.discovery.service';
import {
    RestInfoContext,
    CurrentViewContext,
    SortingContext,
    SortOrderContext,
    LoadedLocationsMapContext,
    MarkedLocationIdContext,
    ContentTypesMapContext,
    SelectedLocationsContext,
    MultipleConfigContext,
    ContainersOnlyContext,
    AllowedContentTypesContext,
    RootLocationIdContext,
} from '../../universal.discovery.module';

const ContentTableItem = ({ location }) => {
    const restInfo = useContext(RestInfoContext);
    const [currentView, setCurrentView] = useContext(CurrentViewContext);
    const [sorting, setSorting] = useContext(SortingContext);
    const [sortOrder, setSortOrder] = useContext(SortOrderContext);
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useContext(LoadedLocationsMapContext);
    const [markedLocationId, setMarkedLocationId] = useContext(MarkedLocationIdContext);
    const contentTypesMap = useContext(ContentTypesMapContext);
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const [multiple, multipleItemsLimit] = useContext(MultipleConfigContext);
    const rootLocationId = useContext(RootLocationIdContext);
    const { formatShortDateTime } = window.eZ.helpers.timezone;
    const allowedContentTypes = useContext(AllowedContentTypesContext);
    const contentTypeInfo = contentTypesMap[location.ContentInfo.Content.ContentType._href];
    const containersOnly = useContext(ContainersOnlyContext);
    const isContainer = contentTypeInfo.isContainer;
    const isNotSelectable =
        (containersOnly && !isContainer) || (allowedContentTypes && !allowedContentTypes.includes(contentTypeInfo.identifier));
    const className = createCssClassNames({
        'ibexa-table__row c-content-table-item': true,
        'c-content-table-item--marked': markedLocationId === location.id,
        'c-content-table-item--not-selectable': isNotSelectable,
    });
    const markLocation = ({ nativeEvent }) => {
        const isSelectionButtonClicked = nativeEvent.target.closest('.c-toggle-selection-button');

        if (isSelectionButtonClicked) {
            return;
        }

        setMarkedLocationId(location.id);
        loadAccordionData(
            {
                ...restInfo,
                parentLocationId: location.id,
                sortClause: sorting,
                sortOrder: sortOrder,
                gridView: currentView === 'grid',
                rootLocationId,
            },
            (locationsMap) => {
                dispatchLoadedLocationsAction({ type: 'SET_LOCATIONS', data: locationsMap });
            }
        );

        if (!multiple && !isNotSelectable) {
            dispatchSelectedLocationsAction({ type: 'CLEAR_SELECTED_LOCATIONS' });
            dispatchSelectedLocationsAction({ type: 'ADD_SELECTED_LOCATION', location });
        }
    };
    const renderToggleSelection = () => {
        return <ToggleSelection location={location} multiple={multiple} isHidden={isNotSelectable} />;
    };

    return (
        <tr className={className} onClick={markLocation}>
            <td className="ibexa-table__cell ibexa-table__cell--has-checkbox">
                {renderToggleSelection()}
            </td>
            <td className="ibexa-table__cell c-content-table-item__icon-wrapper">
                <Icon extraClasses="ibexa-icon--small" customPath={contentTypeInfo.thumbnail} />
            </td>
            <td className="ibexa-table__cell">
                {location.ContentInfo.Content.TranslatedName}
            </td>
            <td className="ibexa-table__cell">
                {formatShortDateTime(new Date(location.ContentInfo.Content.lastModificationDate))}
            </td>
            <td className="ibexa-table__cell">
                {contentTypeInfo.name}
            </td>
        </tr>
    );
};

ContentTableItem.propTypes = {
    location: PropTypes.object.isRequired,
};

export default ContentTableItem;
