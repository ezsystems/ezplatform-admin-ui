import React, { useContext, useState, useEffect, useRef, Fragment } from 'react';
import PropTypes from 'prop-types';

import FinderLeaf from './finder.leaf';
import Icon from '../../../common/icon/icon';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import { useFindLocationsByParentLocationIdFetch } from '../../hooks/useFindLocationsByParentLocationIdFetch';
import {
    LoadedLocationsMapContext,
    SortingContext,
    SortOrderContext,
    ContentTypesMapContext,
    MarkedLocationIdContext,
    SORTING_OPTIONS,
} from '../../universal.discovery.module';

const CLASS_IS_BRANCH_RESIZING = 'ez-is-branch-resizing';
const SCROLL_OFFSET = 200;

const FinderBranch = ({ locationData, itemsPerPage }) => {
    const [offset, setOffset] = useState(0);
    const [branchWidth, setBranchWidth] = useState(0);
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useContext(LoadedLocationsMapContext);
    const [sorting, setSorting] = useContext(SortingContext);
    const [sortOrder, setSortOrder] = useContext(SortOrderContext);
    const contentTypesMap = useContext(ContentTypesMapContext);
    const [markedLocationId, setMarkedLocationId] = useContext(MarkedLocationIdContext);
    const branchRef = useRef(null);
    const sortingOptions = SORTING_OPTIONS.find((option) => option.sortClause === sorting);
    const [loadedLocations, isLoading] = useFindLocationsByParentLocationIdFetch(
        locationData,
        { sortClause: sortingOptions.sortClause, sortOrder },
        itemsPerPage,
        offset
    );
    const { subitems, collapsed } = locationData;
    let resizeStartPositionX = 0;
    let branchCurrentWidth = 0;
    const loadMore = ({ target }) => {
        const areAllItemsLoaded = locationData.subitems.length >= loadedLocations.totalCount;
        const isOffsetReached = target.scrollHeight - target.clientHeight - target.scrollTop < SCROLL_OFFSET;

        if (areAllItemsLoaded || !isOffsetReached || isLoading) {
            return;
        }

        setOffset(offset + itemsPerPage);
    };
    const expandBranch = () => {
        dispatchLoadedLocationsAction({ type: 'UPDATE_LOCATIONS', data: { ...locationData, collapsed: false } });
    };
    const changeBranchWidth = ({ clientX }) => {
        let newBranchWidth = branchCurrentWidth + (clientX - resizeStartPositionX);

        if (newBranchWidth < 50) {
            dispatchLoadedLocationsAction({ type: 'UPDATE_LOCATIONS', data: { ...locationData, collapsed: true } });
            newBranchWidth = 0;
            removeResizeListeners();
        }

        setBranchWidth(newBranchWidth);
    };
    const removeResizeListeners = () => {
        window.document.removeEventListener('mousemove', changeBranchWidth);
        window.document.removeEventListener('mouseup', removeResizeListeners);
        window.document.body.classList.remove(CLASS_IS_BRANCH_RESIZING);
    };
    const addResizeListeners = ({ nativeEvent }) => {
        resizeStartPositionX = nativeEvent.clientX;
        branchCurrentWidth = branchRef.current.getBoundingClientRect().width;

        window.document.addEventListener('mousemove', changeBranchWidth, false);
        window.document.addEventListener('mouseup', removeResizeListeners, false);
        window.document.body.classList.add(CLASS_IS_BRANCH_RESIZING);
    };
    const renderCollapsedBranch = () => {
        if (!collapsed) {
            return null;
        }

        const selectedLocation = subitems.find(
            (subitem) =>
                loadedLocationsMap.find((loadedLocation) => loadedLocation.parentLocationId === subitem.location.id) ||
                subitem.location.id === markedLocationId
        );
        const contentName = selectedLocation ? selectedLocation.location.ContentInfo.Content.TranslatedName : '';
        const iconPath = locationData.location
            ? contentTypesMap[locationData.location.ContentInfo.Content.ContentType._href].thumbnail
            : window.eZ.helpers.icon.getIconPath('folder');

        return (
            <div className="c-finder-branch__info-wrapper">
                <span className="c-finder-branch__icon-wrapper">
                    <Icon extraClasses="ibexa-icon--small" customPath={iconPath} />
                </span>
                <span className="c-finder-branch__name">{contentName}</span>
            </div>
        );
    };
    const renderDragHandler = () => {
        return <div className="c-finder-branch__resize-handler" onMouseDown={addResizeListeners} />;
    };
    const renderSubitems = () => {
        if (collapsed) {
            return null;
        }

        const width = branchWidth ? branchWidth : null;

        return (
            <Fragment>
                <div className="c-finder-branch__items-wrapper" onScroll={loadMore} style={{ width }}>
                    {renderLoadingSpinner()}

                    {subitems.map(({ location }) => (
                        <FinderLeaf key={location.id} location={location} />
                    ))}
                </div>
                {renderDragHandler()}
            </Fragment>
        );
    };
    const renderLoadingSpinner = () => {
        if (!isLoading) {
            return;
        }

        return (
            <div className="c-finder-branch__loading-spinner">
                <Icon name="spinner" extraClasses="ibexa-icon--medium ibexa-spin" />
            </div>
        );
    }

    useEffect(() => {
        if (loadedLocations.subitems) {
            const data = { ...locationData, ...loadedLocations, subitems: [...locationData.subitems, ...loadedLocations.subitems] };

            dispatchLoadedLocationsAction({ type: 'UPDATE_LOCATIONS', data });
        }
    }, [loadedLocations, dispatchLoadedLocationsAction, isLoading]);

    if (!subitems.length && !collapsed && !isLoading) {
        return null;
    }

    const className = createCssClassNames({
        'c-finder-branch': true,
        'c-finder-branch--collapsed': collapsed,
    });
    const onClick = collapsed ? expandBranch : null;

    return (
        <div className={className} onClick={onClick} ref={branchRef}>
            {renderCollapsedBranch()}
            {renderSubitems()}
        </div>
    );
};

FinderBranch.propTypes = {
    locationData: PropTypes.shape({
        parentLocationId: PropTypes.number.isRequired,
        subitems: PropTypes.array.isRequired,
        location: PropTypes.object.isRequired,
        collapsed: PropTypes.bool,
    }).isRequired,
    itemsPerPage: PropTypes.number,
};

FinderBranch.defaultProps = {
    itemsPerPage: 50,
};

export default FinderBranch;
