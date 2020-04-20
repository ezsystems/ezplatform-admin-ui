import React, { useContext, useMemo } from 'react';
import PropTypes from 'prop-types';

import ContentTreeModule from '../../../content-tree/content.tree.module';
import { findLocationsByParentLocationId, loadAccordionData } from '../../services/universal.discovery.service';
import { getLocationData } from '../../content.meta.preview.module';
import {
    AllowedContentTypesContext,
    ContainersOnlyContext,
    ContentTypesMapContext,
    LoadedLocationsMapContext,
    MarkedLocationIdContext,
    MultipleConfigContext,
    RestInfoContext,
    RootLocationIdContext,
    SortingContext,
    SortOrderContext,
    SORTING_OPTIONS,
} from '../../universal.discovery.module';

const flattenTree = (tree) => tree.reduce((output, branch) => [...output, branch.locationId, ...flattenTree(branch.subitems)], []);

const TreeView = ({ itemsPerPage }) => {
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useContext(LoadedLocationsMapContext);
    const [markedLocationId, setMarkedLocationId] = useContext(MarkedLocationIdContext);
    const [multiple, multipleItemsLimit] = useContext(MultipleConfigContext);
    const [sorting, setSorting] = useContext(SortingContext);
    const [sortOrder, setSortOrder] = useContext(SortOrderContext);
    const allowedContentTypes = useContext(AllowedContentTypesContext);
    const containersOnly = useContext(ContainersOnlyContext);
    const contentTypesMap = useContext(ContentTypesMapContext);
    const restInfo = useContext(RestInfoContext);
    const rootLocationId = useContext(RootLocationIdContext);
    const locationData = useMemo(() => getLocationData(loadedLocationsMap, markedLocationId), [markedLocationId, loadedLocationsMap]);
    const sortingOptions = SORTING_OPTIONS.find((option) => option.sortClause === sorting);
    const expandItem = (item, event) => {
        event.preventDefault();
        event.currentTarget
            .closest('.c-list-item__label')
            .querySelector('.c-list-item__toggler')
            .click();
    };
    const markLocation = (item, isExpanded) => {
        const { locationId } = item;

        if (locationId === markedLocationId) {
            return;
        }

        findLocationsByParentLocationId(
            {
                ...restInfo,
                parentLocationId: locationId,
                sortClause: sortingOptions.sortClause,
                sortOrder,
                limit: itemsPerPage,
                offset: 0,
            },
            (response) => {
                const { location } = response;
                const contentTypeInfo = contentTypesMap[location.ContentInfo.Content.ContentType._href];
                const isContainer = contentTypeInfo.isContainer;
                const isNotSelectable =
                    (containersOnly && !isContainer) || (allowedContentTypes && !allowedContentTypes.includes(contentTypeInfo.identifier));

                setMarkedLocationId(locationId);
                dispatchLoadedLocationsAction({ type: 'CUT_LOCATIONS', locationId: markedLocationId });
                dispatchLoadedLocationsAction({ type: 'UPDATE_LOCATIONS', data: { ...response } });

                if (!multiple && !isNotSelectable) {
                    dispatchSelectedLocationsAction({ type: 'CLEAR_SELECTED_LOCATIONS' });
                    dispatchSelectedLocationsAction({ type: 'ADD_SELECTED_LOCATION', location });
                }
            }
        );
    };
    const readSubtree = () => {
        const tree = [];
        let leafs = tree;

        loadedLocationsMap.forEach((location) => {
            leafs.push({
                children: [],
                limit: 30,
                locationId: location.parentLocationId,
                offset: 0,
                '_media-type': 'application/vnd.ez.api.ContentTreeLoadSubtreeRequestNode',
            });
            leafs = leafs[0].children;
        });

        return tree;
    };
    const currentLocationPath = locationData && locationData.location ? locationData.location.pathString : '/1/';
    const locationsLoaded = loadedLocationsMap.length > 1 || loadedLocationsMap[0].subitems.length > 0;
    const contentTreeVisible = (markedLocationId !== null && locationsLoaded) || markedLocationId === null;

    console.log(loadedLocationsMap);

    return (
        <div className="c-tree">
            {contentTreeVisible && (
                <ContentTreeModule
                    userId={14}
                    currentLocationPath={currentLocationPath}
                    rootLocationId={rootLocationId}
                    restInfo={restInfo}
                    onClickItem={expandItem}
                    readSubtree={readSubtree}
                    afterItemToggle={markLocation}
                />
            )}
        </div>
    );
};

TreeView.propTypes = {
    itemsPerPage: PropTypes.number,
};

TreeView.defaultProps = {
    itemsPerPage: 50,
};

export default TreeView;
