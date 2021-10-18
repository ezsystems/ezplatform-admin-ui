import React, { useContext, useState, useMemo, useEffect, useCallback } from 'react';

import Icon from '../../../common/icon/icon';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import { LoadedLocationsMapContext } from '../../universal.discovery.module';

const Breadcrumbs = () => {
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useContext(LoadedLocationsMapContext);
    const [hiddenListVisible, setHiddenListVisible] = useState(false);
    const { visibleItems, hiddenItems } = useMemo(() => {
        return loadedLocationsMap.reduce(
            (splittedItems, loadedLocation, index) => {
                if (loadedLocationsMap.length - 3 <= index) {
                    splittedItems.visibleItems.push(loadedLocation);
                } else {
                    splittedItems.hiddenItems.push(loadedLocation);
                }

                return splittedItems;
            },
            { visibleItems: [], hiddenItems: [] }
        );
    }, [loadedLocationsMap]);
    const goToLocation = (locationId) => {
        const itemIndex = loadedLocationsMap.findIndex((data) => data.parentLocationId === locationId);
        const updatedLoadedLocations = loadedLocationsMap.slice(0, itemIndex + 1);

        updatedLoadedLocations[updatedLoadedLocations.length - 1].subitems = [];

        dispatchLoadedLocationsAction({ type: 'SET_LOCATIONS', data: updatedLoadedLocations });
    };
    const toggleHiddenListVisible = useCallback(() => {
        setHiddenListVisible(!hiddenListVisible);
    }, [setHiddenListVisible, hiddenListVisible]);
    const renderHiddenList = () => {
        if (!hiddenItems.length) {
            return null;
        }

        const hiddenListClassNames = createCssClassNames({
            'c-breadcrumbs__hidden-list': true,
            'c-breadcrumbs__hidden-list--visible': hiddenListVisible,
        });
        const toggleClassNames = createCssClassNames({
            'c-breadcrumbs__hidden-list-toggler': true,
            'c-breadcrumbs__hidden-list-toggler--active': hiddenListVisible,
        });

        return (
            <div className="c-breadcrumbs__hidden-list-wrapper">
                <button className={toggleClassNames} onClick={toggleHiddenListVisible}>
                    <Icon name="options" extraClasses="ibexa-icon--small-medium" />
                </button>
                <ul className={hiddenListClassNames}>
                    {hiddenItems.map((item) => {
                        const locationId = item.parentLocationId;
                        const locationName =
                            locationId === 1
                                ? Translator.trans(/*@Desc("Root Location")*/ 'breadcrumbs.root_location', {}, 'universal_discovery_widget')
                                : item.location.ContentInfo.Content.TranslatedName;
                        const onClickHandler = goToLocation.bind(this, locationId);

                        return (
                            <li key={locationId} onClick={onClickHandler} className="c-breadcrumbs__hidden-list-item">
                                {locationName}
                            </li>
                        );
                    })}
                </ul>
            </div>
        );
    };
    const renderSeparator = () => {
        return <span className="c-breadcrumbs__list-item-separator">/</span>;
    };

    useEffect(() => {
        if (hiddenListVisible) {
            window.document.body.addEventListener('click', toggleHiddenListVisible, false);
        } else {
            window.document.body.removeEventListener('click', toggleHiddenListVisible, false);
        }

        return () => window.document.body.removeEventListener('click', toggleHiddenListVisible, false);
    }, [hiddenListVisible, toggleHiddenListVisible]);

    if (loadedLocationsMap.some((loadedLocation) => loadedLocation.parentLocationId !== 1 && !loadedLocation.location)) {
        return null;
    }

    return (
        <div className="c-breadcrumbs">
            {renderHiddenList()}
            <div className="c-breadcrumbs__list-wrapper">
                <ul className="c-breadcrumbs__list">
                    {visibleItems.map((item, index) => {
                        const locationId = item.parentLocationId;
                        const locationName =
                            locationId === 1
                                ? Translator.trans(/*@Desc("Root Location")*/ 'breadcrumbs.root_location', {}, 'universal_discovery_widget')
                                : item.location.ContentInfo.Content.TranslatedName;
                        const isLast = index === visibleItems.length - 1;
                        const onClickHandler = goToLocation.bind(this, locationId);
                        const className = createCssClassNames({
                            'c-breadcrumbs__list-item': true,
                            'c-breadcrumbs__list-item--last': isLast,
                        });

                        return (
                            <li key={locationId} onClick={onClickHandler} className={className}>
                                <span className="c-breadcrumbs__list-item-text">{locationName}</span>
                                {!isLast && renderSeparator()}
                            </li>
                        );
                    })}
                </ul>
            </div>
        </div>
    );
};

export default Breadcrumbs;
