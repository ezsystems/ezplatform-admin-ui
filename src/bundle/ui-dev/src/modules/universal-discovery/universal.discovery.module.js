import React, { useEffect, useState, createContext } from 'react';
import PropTypes from 'prop-types';

import deepClone from '../common/helpers/deep.clone.helper';
import { createCssClassNames } from '../common/helpers/css.class.names';
import { useLoadedLocationsReducer } from './hooks/useLoadedLocationsReducer';
import { useSelectedLocationsReducer } from './hooks/useSelectedLocationsReducer';
import {
    loadAccordionData,
    loadContentTypes,
    findLocationsById,
    loadContentInfo,
    loadLocationsWithPermissions,
} from './services/universal.discovery.service';

const CLASS_SCROLL_DISABLED = 'ez-scroll-disabled';

export const SORTING_OPTIONS = [
    {
        label: Translator.trans(/*@Desc("Date")*/ 'sorting.date.label', {}, 'universal_discovery_widget'),
        tooltipLabel: Translator.trans(/*@Desc("Sort by Date")*/ 'sorting.date.tooltip', {}, 'universal_discovery_widget'),
        sortClause: 'DatePublished',
    },
    {
        label: Translator.trans(/*@Desc("Name")*/ 'sorting.name.label', {}, 'universal_discovery_widget'),
        tooltipLabel: Translator.trans(/*@Desc("Sort by Name")*/ 'sorting.name.tooltip', {}, 'universal_discovery_widget'),
        sortClause: 'ContentName',
    },
];
export const VIEWS = [
    {
        id: 'grid',
        icon: 'view-grid',
        tooltipLabel: Translator.trans(/*@Desc("Grid view")*/ 'sorting.grid.view', {}, 'universal_discovery_widget'),
    },
    {
        id: 'finder',
        icon: 'panels',
        tooltipLabel: Translator.trans(/*@Desc("Panels view")*/ 'sorting.panels.view', {}, 'universal_discovery_widget'),
    },
    {
        id: 'tree',
        icon: 'content-tree',
        tooltipLabel: Translator.trans(/*@Desc("Tree view")*/ 'sorting.tree.view', {}, 'universal_discovery_widget'),
    },
];

const restInfo = {
    token: document.querySelector('meta[name="CSRF-Token"]').content,
    siteaccess: document.querySelector('meta[name="SiteAccess"]').content,
};
const contentTypesMap = Object.values(eZ.adminUiConfig.contentTypes).reduce((contentTypesMap, contentTypesGroup) => {
    contentTypesGroup.forEach((contentType) => {
        contentTypesMap[contentType.href] = contentType;
    });

    return contentTypesMap;
}, {});

export const RestInfoContext = createContext();
export const AllowRedirectsContext = createContext();
export const AllowConfirmationContext = createContext();
export const ContentTypesMapContext = createContext();
export const ContentTypesInfoMapContext = createContext();
export const MultipleConfigContext = createContext();
export const ContainersOnlyContext = createContext();
export const AllowedContentTypesContext = createContext();
export const ActiveTabContext = createContext();
export const TabsConfigContext = createContext();
export const TabsContext = createContext();
export const TitleContext = createContext();
export const CancelContext = createContext();
export const ConfirmContext = createContext();
export const SortingContext = createContext();
export const SortOrderContext = createContext();
export const CurrentViewContext = createContext();
export const MarkedLocationIdContext = createContext();
export const LoadedLocationsMapContext = createContext();
export const RootLocationIdContext = createContext();
export const SelectedLocationsContext = createContext();
export const CreateContentWidgetContext = createContext();
export const ContentOnTheFlyDataContext = createContext();
export const ContentOnTheFlyConfigContext = createContext();
export const EditOnTheFlyDataContext = createContext();

const UniversalDiscoveryModule = (props) => {
    const tabs = window.eZ.adminUiConfig.universalDiscoveryWidget.tabs;
    const defaultMarkedLocationId = props.startingLocationId || props.rootLocationId;
    const [activeTab, setActiveTab] = useState(props.activeTab);
    const [sorting, setSorting] = useState(props.activeSortClause);
    const [sortOrder, setSortOrder] = useState(props.activeSortOrder);
    const [currentView, setCurrentView] = useState(props.activeView);
    const [markedLocationId, setMarkedLocationId] = useState(defaultMarkedLocationId !== 1 ? defaultMarkedLocationId : null);
    const [createContentVisible, setCreateContentVisible] = useState(false);
    const [contentOnTheFlyData, setContentOnTheFlyData] = useState({});
    const [editOnTheFlyData, setEditOnTheFlyData] = useState({});
    const [contentTypesInfoMap, setContentTypesInfoMap] = useState({});
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useLoadedLocationsReducer([
        { parentLocationId: props.rootLocationId, subitems: [] },
    ]);
    const [selectedLocations, dispatchSelectedLocationsAction] = useSelectedLocationsReducer();
    const activeTabConfig = tabs.find((tab) => tab.id === activeTab);
    const Tab = activeTabConfig.component;
    const className = createCssClassNames({
        'm-ud': true,
        'm-ud--locations-selected': !!selectedLocations.length && props.allowConfirmation,
    });
    const onConfirm = (selectedItems = selectedLocations) => {
        const updatedLocations = selectedItems.map((selectedItem) => {
            const clonedLocation = deepClone(selectedItem.location);
            const contentType = clonedLocation.ContentInfo.Content.ContentType;

            clonedLocation.ContentInfo.Content.ContentTypeInfo = contentTypesInfoMap[contentType._href];

            return clonedLocation;
        });

        props.onConfirm(updatedLocations);
    };
    const addPermissionsToSelectedLocations = (response) => {
        const clonedSelectedLocation = deepClone(selectedLocations);

        response.forEach((item) => {
            const locationWithoutPermissions = clonedSelectedLocation.find(
                (selectedItem) => selectedItem.location.id === item.location.Location.id
            );

            if (locationWithoutPermissions) {
                locationWithoutPermissions.permissions = item.permissions;
            }
        });

        dispatchSelectedLocationsAction({
            type: 'REPLACE_SELECTED_LOCATIONS',
            locations: clonedSelectedLocation,
        });
    };

    useEffect(() => {
        const handleLoadContentTypes = (response) => {
            const contentTypesMap = response.ContentTypeInfoList.ContentType.reduce((contentTypesList, item) => {
                contentTypesList[item._href] = item;

                return contentTypesList;
            }, {});

            setContentTypesInfoMap(contentTypesMap);
        };

        loadContentTypes(restInfo, handleLoadContentTypes);
        window.document.body.dispatchEvent(new CustomEvent('ez-udw-opened'));
        window.eZ.helpers.tooltips.parse(window.document.querySelector('.c-udw-tab'));

        return () => {
            window.document.body.dispatchEvent(new CustomEvent('ez-udw-closed'));
        };
    }, []);

    useEffect(() => {
        if (!props.selectedLocations.length) {
            return;
        }

        loadLocationsWithPermissions({ locationIds: props.selectedLocations.join(',') }, addPermissionsToSelectedLocations);
    }, [props.selectedLocations]);

    useEffect(() => {
        const locationIds = selectedLocations
            .filter((item) => !item.permissions)
            .map((item) => item.location.id)
            .join(',');

        if (!locationIds) {
            return;
        }

        loadLocationsWithPermissions({ locationIds }, addPermissionsToSelectedLocations);
    }, [selectedLocations]);

    useEffect(() => {
        const locationsWithoutVersion = selectedLocations.filter(
            (selectedItem) => !selectedItem.location.ContentInfo.Content.CurrentVersion.Version
        );

        if (!locationsWithoutVersion.length) {
            return;
        }

        const contentId = locationsWithoutVersion.map((item) => item.location.ContentInfo.Content._id).join(',');

        loadContentInfo(
            {
                ...restInfo,
                contentId,
            },
            (response) => {
                const clonedLocations = selectedLocations;

                response.forEach((content) => {
                    const clonedLocation = clonedLocations.find(
                        (clonedItem) => clonedItem.location.ContentInfo.Content._id === content._id
                    );

                    if (clonedLocation) {
                        clonedLocation.location.ContentInfo.Content.CurrentVersion.Version = content.CurrentVersion.Version;
                    }
                });

                dispatchSelectedLocationsAction({ type: 'REPLACE_SELECTED_LOCATIONS', locations: clonedLocations });
            }
        );
    }, [selectedLocations]);

    useEffect(() => {
        window.document.body.classList.add(CLASS_SCROLL_DISABLED);

        return () => {
            window.document.body.classList.remove(CLASS_SCROLL_DISABLED);
        };
    });

    useEffect(() => {
        if (currentView === 'grid') {
            loadedLocationsMap[loadedLocationsMap.length - 1].subitems = [];

            dispatchLoadedLocationsAction({ type: 'SET_LOCATIONS', data: loadedLocationsMap });
        } else if (
            (currentView === 'finder' || currentView === 'tree') &&
            !!markedLocationId &&
            markedLocationId !== loadedLocationsMap[loadedLocationsMap.length - 1].parentLocationId &&
            loadedLocationsMap[loadedLocationsMap.length - 1].subitems.find((subitem) => subitem.location.id === markedLocationId)
        ) {
            dispatchLoadedLocationsAction({ type: 'UPDATE_LOCATIONS', data: { parentLocationId: markedLocationId, subitems: [] } });
        }
    }, [currentView]);

    useEffect(() => {
        if (!props.startingLocationId || props.startingLocationId === 1) {
            return;
        }

        loadAccordionData(
            {
                ...restInfo,
                parentLocationId: props.startingLocationId,
                sortClause: sorting,
                sortOrder: sortOrder,
                gridView: currentView === 'grid',
                rootLocationId: props.rootLocationId,
            },
            (locationsMap) => {
                dispatchLoadedLocationsAction({ type: 'SET_LOCATIONS', data: locationsMap });
                setMarkedLocationId(props.startingLocationId);
            }
        );
    }, [props.startingLocationId]);

    useEffect(() => {
        const locationsMap = loadedLocationsMap.map((loadedLocation) => {
            loadedLocation.subitems = [];

            return loadedLocation;
        });

        dispatchLoadedLocationsAction({ type: 'SET_LOCATIONS', data: locationsMap });
    }, [sorting, sortOrder]);

    return (
        <div className={className}>
            <RestInfoContext.Provider value={restInfo}>
                <AllowRedirectsContext.Provider value={props.allowRedirects}>
                    <AllowConfirmationContext.Provider value={props.allowConfirmation}>
                        <ContentTypesInfoMapContext.Provider value={contentTypesInfoMap}>
                            <ContentTypesMapContext.Provider value={contentTypesMap}>
                                <MultipleConfigContext.Provider value={[props.multiple, props.multipleItemsLimit]}>
                                    <ContainersOnlyContext.Provider value={props.containersOnly}>
                                        <AllowedContentTypesContext.Provider value={props.allowedContentTypes}>
                                            <ActiveTabContext.Provider value={[activeTab, setActiveTab]}>
                                                <TabsContext.Provider value={tabs}>
                                                    <TabsConfigContext.Provider value={props.tabsConfig}>
                                                        <TitleContext.Provider value={props.title}>
                                                            <CancelContext.Provider value={props.onCancel}>
                                                                <ConfirmContext.Provider value={onConfirm}>
                                                                    <SortingContext.Provider value={[sorting, setSorting]}>
                                                                        <SortOrderContext.Provider value={[sortOrder, setSortOrder]}>
                                                                            <CurrentViewContext.Provider
                                                                                value={[currentView, setCurrentView]}>
                                                                                <MarkedLocationIdContext.Provider
                                                                                    value={[markedLocationId, setMarkedLocationId]}>
                                                                                    <LoadedLocationsMapContext.Provider
                                                                                        value={[
                                                                                            loadedLocationsMap,
                                                                                            dispatchLoadedLocationsAction,
                                                                                        ]}>
                                                                                        <RootLocationIdContext.Provider
                                                                                            value={props.rootLocationId}>
                                                                                            <SelectedLocationsContext.Provider
                                                                                                value={[
                                                                                                    selectedLocations,
                                                                                                    dispatchSelectedLocationsAction,
                                                                                                ]}>
                                                                                                <CreateContentWidgetContext.Provider
                                                                                                    value={[
                                                                                                        createContentVisible,
                                                                                                        setCreateContentVisible,
                                                                                                    ]}>
                                                                                                    <ContentOnTheFlyDataContext.Provider
                                                                                                        value={[
                                                                                                            contentOnTheFlyData,
                                                                                                            setContentOnTheFlyData,
                                                                                                        ]}>
                                                                                                        <ContentOnTheFlyConfigContext.Provider
                                                                                                            value={props.contentOnTheFly}>
                                                                                                            <EditOnTheFlyDataContext.Provider
                                                                                                                value={[
                                                                                                                    editOnTheFlyData,
                                                                                                                    setEditOnTheFlyData,
                                                                                                                ]}>
                                                                                                                <Tab />
                                                                                                            </EditOnTheFlyDataContext.Provider>
                                                                                                        </ContentOnTheFlyConfigContext.Provider>
                                                                                                    </ContentOnTheFlyDataContext.Provider>
                                                                                                </CreateContentWidgetContext.Provider>
                                                                                            </SelectedLocationsContext.Provider>
                                                                                        </RootLocationIdContext.Provider>
                                                                                    </LoadedLocationsMapContext.Provider>
                                                                                </MarkedLocationIdContext.Provider>
                                                                            </CurrentViewContext.Provider>
                                                                        </SortOrderContext.Provider>
                                                                    </SortingContext.Provider>
                                                                </ConfirmContext.Provider>
                                                            </CancelContext.Provider>
                                                        </TitleContext.Provider>
                                                    </TabsConfigContext.Provider>
                                                </TabsContext.Provider>
                                            </ActiveTabContext.Provider>
                                        </AllowedContentTypesContext.Provider>
                                    </ContainersOnlyContext.Provider>
                                </MultipleConfigContext.Provider>
                            </ContentTypesMapContext.Provider>
                        </ContentTypesInfoMapContext.Provider>
                    </AllowConfirmationContext.Provider>
                </AllowRedirectsContext.Provider>
            </RestInfoContext.Provider>
        </div>
    );
};

UniversalDiscoveryModule.propTypes = {
    onConfirm: PropTypes.func.isRequired,
    onCancel: PropTypes.func.isRequired,
    title: PropTypes.string.isRequired,
    activeTab: PropTypes.string,
    rootLocationId: PropTypes.number,
    startingLocationId: PropTypes.number,
    multiple: PropTypes.bool,
    multipleItemsLimit: PropTypes.number,
    containersOnly: PropTypes.bool,
    allowedContentTypes: PropTypes.array.isRequired,
    activeSortClause: PropTypes.string,
    activeSortOrder: PropTypes.string,
    activeView: PropTypes.string,
    contentOnTheFly: PropTypes.shape({
        allowedLanguages: PropTypes.array.isRequired,
        allowedLocations: PropTypes.array.isRequired,
        preselectedLocation: PropTypes.string.isRequired,
        preselectedContentType: PropTypes.string.isRequired,
        hidden: PropTypes.bool.isRequired,
        autoConfirmAfterPublish: PropTypes.bool.isRequired,
    }).isRequired,
    tabsConfig: PropTypes.objectOf(
        PropTypes.shape({
            itemsPerPage: PropTypes.number.isRequired,
            priority: PropTypes.number.isRequired,
            hidden: PropTypes.bool.isRequired,
        })
    ).isRequired,
    selectedLocations: PropTypes.array,
    allowRedirects: PropTypes.bool.isRequired,
    allowConfirmation: PropTypes.bool.isRequired,
};

UniversalDiscoveryModule.defaultProps = {
    activeTab: 'browse',
    rootLocationId: 1,
    startingLocationId: null,
    multiple: false,
    multipleItemsLimit: 1,
    containersOnly: false,
    activeSortClause: 'date',
    activeSortOrder: 'ascending',
    activeView: 'tree',
    tabs: window.eZ.adminUiConfig.universalDiscoveryWidget.tabs,
    selectedLocations: [],
};

eZ.addConfig('modules.UniversalDiscovery', UniversalDiscoveryModule);

export default UniversalDiscoveryModule;
