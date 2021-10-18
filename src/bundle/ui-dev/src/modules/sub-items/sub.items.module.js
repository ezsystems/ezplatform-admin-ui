import React, { Component, Fragment } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

import ViewSwitcherComponent from './components/view-switcher/view.switcher.component.js';
import SubItemsListComponent from './components/sub-items-list/sub.items.list.component.js';
import Popup from '../common/popup/popup.component';
import ActionButton from './components/action-btn/action.btn.js';
import Pagination from '../common/pagination/pagination.js';
import NoItemsComponent from './components/no-items/no.items.component.js';
import Icon from '../common/icon/icon.js';

import deepClone from '../common/helpers/deep.clone.helper.js';
import { updateLocationPriority, loadLocation } from './services/sub.items.service';
import { bulkAddLocations, bulkDeleteItems, bulkHideLocations, bulkUnhideLocations, bulkMoveLocations } from './services/bulk.service.js';

export const ASCENDING_SORT_ORDER = 'ascending';
const DESCENDING_SORT_ORDER = 'descending';
const DEFAULT_SORT_ORDER = ASCENDING_SORT_ORDER;
const ACTION_FLOW_ADD_LOCATIONS = 'add';
const ACTION_FLOW_MOVE = 'move';
const SUBITEMS_PADDING = 15;

export const VIEW_MODE_TABLE = 'table';
export const VIEW_MODE_GRID = 'grid';

export default class SubItemsModule extends Component {
    constructor(props) {
        super(props);

        this.afterPriorityUpdated = this.afterPriorityUpdated.bind(this);
        this.switchView = this.switchView.bind(this);
        this.handleItemPriorityUpdate = this.handleItemPriorityUpdate.bind(this);
        this.toggleItemSelection = this.toggleItemSelection.bind(this);
        this.toggleAllPageItemsSelection = this.toggleAllPageItemsSelection.bind(this);
        this.onMoveBtnClick = this.onMoveBtnClick.bind(this);
        this.closeUdw = this.closeUdw.bind(this);
        this.onUdwConfirm = this.onUdwConfirm.bind(this);
        this.onDeleteBtnClick = this.onDeleteBtnClick.bind(this);
        this.onAddLocationsBtnClick = this.onAddLocationsBtnClick.bind(this);
        this.onHideBtnClick = this.onHideBtnClick.bind(this);
        this.onUnhideBtnClick = this.onUnhideBtnClick.bind(this);
        this.closeBulkDeletePopup = this.closeBulkDeletePopup.bind(this);
        this.closeBulkHidePopup = this.closeBulkHidePopup.bind(this);
        this.closeBulkUnhidePopup = this.closeBulkUnhidePopup.bind(this);
        this.onBulkDeletePopupConfirm = this.onBulkDeletePopupConfirm.bind(this);
        this.onBulkHidePopupConfirm = this.onBulkHidePopupConfirm.bind(this);
        this.onBulkUnhidePopupConfirm = this.onBulkUnhidePopupConfirm.bind(this);
        this.afterBulkDelete = this.afterBulkDelete.bind(this);
        this.afterBulkHide = this.afterBulkHide.bind(this);
        this.afterBulkUnhide = this.afterBulkUnhide.bind(this);
        this.changePage = this.changePage.bind(this);
        this.changeSorting = this.changeSorting.bind(this);
        this.calculateSubItemsWidth = this.calculateSubItemsWidth.bind(this);
        this.resizeSubItems = this.resizeSubItems.bind(this);

        this._refListViewWrapper = React.createRef();
        this.bulkActionModalContainer = null;
        this.udwContainer = null;

        const sortClauseData = this.getDefaultSortClause(props.sortClauses);

        this.state = {
            activeView: props.activeView,
            activePageItems: null,
            pages: [],
            selectedItems: new Map(),
            totalCount: props.totalCount,
            offset: props.offset,
            isDuringBulkOperation: false,
            isUdwOpened: false,
            isBulkDeletePopupVisible: false,
            isBulkHidePopupVisible: false,
            isBulkUnhidePopupVisible: false,
            activePageIndex: 0,
            listViewHeight: null,
            actionFlow: null,
            sortClause: sortClauseData.name,
            sortOrder: sortClauseData.order,
            subItemsWidth: this.calculateSubItemsWidth(),
        };
    }

    componentDidMount() {
        this.udwContainer = document.getElementById('react-udw');
        this.bulkActionModalContainer = document.createElement('div');
        this.bulkActionModalContainer.classList.add('m-sub-items__bulk-action-modal-container');
        document.body.appendChild(this.bulkActionModalContainer);
        document.body.addEventListener('ibexa-main-menu-resized', this.resizeSubItems, false);
        window.addEventListener('resize', this.resizeSubItems, false);

        if (!this.state.activePageItems) {
            this.loadPage(0);
        }
    }

    componentDidUpdate() {
        const { activePageIndex, activePageItems, totalCount } = this.state;
        const { limit: itemsPerPage } = this.props;
        const pagesCount = Math.ceil(totalCount / itemsPerPage);
        const pageDoesNotExist = activePageIndex > pagesCount - 1 && activePageIndex !== 0;

        if (pageDoesNotExist) {
            this.setState({
                activePageIndex: pagesCount - 1,
            });

            return;
        }

        const shouldLoadPage = !activePageItems;

        if (shouldLoadPage) {
            this.loadPage(activePageIndex);
        }

        eZ.helpers.tooltips.parse();
    }

    componentWillUnmount() {
        document.body.removeChild(this.bulkActionModalContainer);
    }

    resizeSubItems() {
        this.setState({ subItemsWidth: this.calculateSubItemsWidth() });
    }

    calculateSubItemsWidth() {
        const sideMenu = document.querySelector('.ibexa-main-container__side-column');
        const sideMenuRect = sideMenu.getBoundingClientRect();
        const windowWidth = window.innerWidth;

        return windowWidth - sideMenuRect.width - SUBITEMS_PADDING;
    }

    getDefaultSortClause(sortClauses) {
        const objKeys = Object.keys(sortClauses);

        if (!objKeys.length) {
            return { name: null, order: null };
        }

        const name = objKeys[0];
        const order = sortClauses[name];

        return { name, order };
    }

    updateListViewHeight() {
        this.setState(() => ({
            listViewHeight: this._refListViewWrapper.current.offsetHeight,
        }));
    }

    /**
     * Loads items into the list
     *
     * @method loadPage
     * @memberof SubItemsModule
     */
    loadPage(pageIndex) {
        const { limit: itemsPerPage, parentLocationId: locationId, loadLocation, restInfo } = this.props;
        const { sortClause, sortOrder } = this.state;
        const page = this.state.pages.find((page) => page.number === pageIndex + 1);
        const cursor = page ? page.cursor : null;
        const queryConfig = { locationId, limit: itemsPerPage, sortClause, sortOrder, cursor };

        loadLocation(restInfo, queryConfig, (response) => {
            const { totalCount, pages, edges } = response.data._repository.location.children;
            const activePageItems = edges.map((edge) => edge.node);

            this.setState(() => ({
                activePageItems,
                totalCount,
                pages,
            }));
        });
    }

    updateTotalCountState(totalCount) {
        this.setState(() => ({
            totalCount,
        }));
    }

    discardActivePageItems() {
        this.updateListViewHeight();
        this.setState(() => ({
            activePageItems: null,
        }));
    }

    changeSorting(sortClause) {
        this.updateListViewHeight();
        this.setState((state) => ({
            sortClause,
            sortOrder: this.getSortOrder(state.sortClause, sortClause, state.sortOrder),
            activePageItems: null,
        }));
    }

    getSortOrder(sortClause, newSortClause, currentSortOrder) {
        return newSortClause === sortClause ? this.getOppositeSortOrder(currentSortOrder) : DEFAULT_SORT_ORDER;
    }

    getOppositeSortOrder(sortOrder) {
        return sortOrder === ASCENDING_SORT_ORDER ? DESCENDING_SORT_ORDER : ASCENDING_SORT_ORDER;
    }

    /**
     * Updates item priority
     *
     * @method handleItemPriorityUpdate
     * @param {Object} data data hash containing: priority, location, token, siteaccess
     * @memberof SubItemsModule
     */
    handleItemPriorityUpdate(data) {
        this.props.updateLocationPriority({ ...data, ...this.props.restInfo }, this.afterPriorityUpdated);
    }

    /**
     * Updates module state after item's priority has been updated
     *
     * @method afterPriorityUpdated
     * @param {Object} response
     * @memberof SubItemsModule
     */
    afterPriorityUpdated(response) {
        if (this.state.sortClause === 'LocationPriority') {
            this.discardActivePageItems();
            this.refreshContentTree();
            return;
        }

        this.updateItemLocation(response.Location);
    }

    updateItemLocation(location) {
        this.setState(({ activePageItems }) => {
            const itemIndex = activePageItems.findIndex((item) => item.id === location.id);

            if (itemIndex === -1) {
                return null;
            }

            const item = activePageItems[itemIndex];
            const updatedItem = deepClone(item);
            const updatedPageItems = [...activePageItems];

            updatedItem.priority = location.priority;
            updatedPageItems[itemIndex] = updatedItem;

            return {
                activePageItems: updatedPageItems,
            };
        });
    }

    /**
     * Switches active view
     *
     * @method switchView
     * @param {String} activeView
     * @memberof SubItemsModule
     */
    switchView(activeView) {
        this.setState(
            () => ({ activeView }),
            () => {
                eZ.helpers.tooltips.hideAll();
                window.localStorage.setItem(`ez-subitems-active-view-location-${this.props.parentLocationId}`, activeView);
            }
        );
    }

    toggleItemSelection(item, isSelected) {
        const { selectedItems } = this.state;
        const updatedSelectedItems = new Map(selectedItems);
        const locationId = item.id;

        if (isSelected) {
            updatedSelectedItems.set(locationId, item);
        } else {
            updatedSelectedItems.delete(locationId);
        }

        this.setState(() => ({ selectedItems: updatedSelectedItems }));
    }

    toggleAllPageItemsSelection(select) {
        const { activePageItems } = this.state;

        if (select) {
            this.selectItems(activePageItems);
        } else {
            const locationsIds = activePageItems.map((item) => item.id);
            const locationsIdsSet = new Set(locationsIds);

            this.deselectItems(locationsIdsSet);
        }
    }

    /**
     *
     * @param {Array} itemsToSelect
     */
    selectItems(itemsToSelect) {
        const { selectedItems } = this.state;
        const newSelectedItems = itemsToSelect.map((item) => [item.id, item]);
        const newSelection = new Map([...selectedItems, ...newSelectedItems]);

        this.setState(() => ({ selectedItems: newSelection }));
    }

    /**
     * Deselects items with locations with provided IDs.
     *
     * @param {Set} locationsIds
     */
    deselectItems(locationsIds) {
        const { selectedItems } = this.state;
        const newSelection = new Map([...selectedItems].filter(([locationId]) => !locationsIds.has(locationId)));

        this.setState(() => ({ selectedItems: newSelection }));
    }

    deselectAllItems() {
        this.setState(() => ({ selectedItems: new Map() }));
    }

    toggleBulkOperationStatusState(isDuringBulkOperation) {
        this.setState(() => ({
            isDuringBulkOperation,
        }));
    }

    onMoveBtnClick() {
        this.setState(() => ({
            actionFlow: ACTION_FLOW_MOVE,
        }));
        this.toggleUdw(true);
    }

    onAddLocationsBtnClick() {
        this.setState(() => ({
            actionFlow: ACTION_FLOW_ADD_LOCATIONS,
        }));
        this.toggleUdw(true);
    }

    bulkMove(location) {
        this.toggleBulkOperationStatusState(true);

        const { restInfo } = this.props;
        const { selectedItems } = this.state;
        const itemsToMove = [...selectedItems.values()];

        bulkMoveLocations(restInfo, itemsToMove, location._href, this.afterBulkMove.bind(this, location));
    }

    bulkAdd(location) {
        this.toggleBulkOperationStatusState(true);

        const { restInfo } = this.props;
        const { selectedItems } = this.state;
        const itemsToAddLocationFor = [...selectedItems.values()];

        bulkAddLocations(restInfo, itemsToAddLocationFor, location._href, this.afterBulkAddLocation.bind(this, location));
    }

    afterBulkMove(location, movedItems, notMovedItems) {
        const { totalCount } = this.state;

        this.refreshContentTree();
        this.updateTotalCountState(totalCount - movedItems.length);
        this.deselectAllItems();
        this.discardActivePageItems();
        this.updateTrashModal();

        this.toggleBulkOperationStatusState(false);

        if (notMovedItems.length) {
            const modalTableTitle = Translator.trans(
                /*@Desc("%itemsCount% Content items cannot be moved")*/
                'bulk_move.error.modal.table_title',
                { itemsCount: notMovedItems.length },
                'sub_items'
            );
            const notificationMessage = Translator.trans(
                /*@Desc("%notMovedCount% of the %totalCount% selected item(s) could not be moved because you do not have proper user permissions. {{ moreInformationLink }} Contact your Administrator.")*/ 'bulk_move.error.message',
                {
                    notMovedCount: notMovedItems.length,
                    totalCount: movedItems.length + notMovedItems.length,
                },
                'sub_items'
            );
            const rawPlaceholdersMap = {
                moreInformationLink: Translator.trans(
                    /*@Desc("<u><a class='ez-notification-btn ez-notification-btn--show-modal'>Click here for more information.</a></u><br>")*/
                    'bulk_action.error.more_info',
                    {},
                    'sub_items'
                ),
            };

            this.handleBulkOperationFailedNotification(notMovedItems, modalTableTitle, notificationMessage, rawPlaceholdersMap);
        }

        if (movedItems.length) {
            const message = Translator.trans(
                /*@Desc("Content item(s) sent to {{ locationLink }}")*/
                'bulk_move.success.message',
                {},
                'sub_items'
            );
            const rawPlaceholdersMap = {
                locationLink: Translator.trans(
                    /*@Desc("<u><a href='%locationHref%'>%locationName%</a></u>")*/
                    'bulk_action.success.link_to_location',
                    {
                        locationName: eZ.helpers.text.escapeHTML(location.ContentInfo.Content.Name),
                        locationHref: this.props.generateLink(location.id, location.ContentInfo.Content._id),
                    },
                    'sub_items'
                ),
            };

            window.eZ.helpers.notification.showSuccessNotification(message, () => {}, rawPlaceholdersMap);
        }
    }

    afterBulkHide(successItems, failedItems) {
        this.deselectAllItems();
        this.discardActivePageItems();
        this.toggleBulkOperationStatusState(false);

        if (failedItems.length) {
            const modalTableTitle = Translator.trans(
                /*@Desc("%itemsCount% Content item(s) cannot be hidden")*/
                'bulk_hide.error.modal.table_title',
                { itemsCount: failedItems.length },
                'sub_items'
            );
            const notificationMessage = Translator.trans(
                /*@Desc("%failedCount% of the %totalCount% selected item(s) could not be hidden because you do not have proper user permissions. {{ moreInformationLink }} Contact your Administrator to obtain permissions.")*/
                'bulk_hide.error.message',
                {
                    failedCount: failedItems.length,
                    totalCount: successItems.length + failedItems.length,
                },
                'sub_items'
            );
            const rawPlaceholdersMap = {
                moreInformationLink: Translator.trans(
                    /*@Desc("<u><a class='ez-notification-btn ez-notification-btn--show-modal'>Click here for more information.</a></u><br>")*/
                    'bulk_action.error.more_info',
                    {},
                    'sub_items'
                ),
            };

            this.handleBulkOperationFailedNotification(failedItems, modalTableTitle, notificationMessage, rawPlaceholdersMap);
        }

        if (successItems.length) {
            const message = Translator.trans(
                /*@Desc("Location(s) hidden.")*/
                'bulk_hide.success.message',
                {},
                'sub_items'
            );

            window.eZ.helpers.notification.showSuccessNotification(message);
        }
    }

    afterBulkUnhide(successItems, failedItems) {
        this.deselectAllItems();
        this.discardActivePageItems();
        this.toggleBulkOperationStatusState(false);

        if (failedItems.length) {
            const modalTableTitle = Translator.trans(
                /*@Desc("%itemsCount% Location(s) cannot be revealed")*/
                'bulk_unhide.error.modal.table_title',
                { itemsCount: failedItems.length },
                'sub_items'
            );
            const notificationMessage = Translator.trans(
                /*@Desc("%failedCount% of the %totalCount% selected Location(s) could not be revealed because you do not have proper user permissions. {{ moreInformationLink }} Contact your Administrator to obtain permissions.")*/
                'bulk_unhide.error.message',
                {
                    failedCount: failedItems.length,
                    totalCount: successItems.length + failedItems.length,
                },
                'sub_items'
            );
            const rawPlaceholdersMap = {
                moreInformationLink: Translator.trans(
                    /*@Desc("<u><a class='ez-notification-btn ez-notification-btn--show-modal'>Click here for more information.</a></u><br>")*/
                    'bulk_action.error.more_info',
                    {},
                    'sub_items'
                ),
            };

            this.handleBulkOperationFailedNotification(failedItems, modalTableTitle, notificationMessage, rawPlaceholdersMap);
        }

        if (successItems.length) {
            const message = Translator.trans(
                /*@Desc("The selected location(s) have been revealed.")*/
                'bulk_unhide.success.message',
                {},
                'sub_items'
            );

            window.eZ.helpers.notification.showSuccessNotification(message);
        }
    }

    afterBulkAddLocation(location, successItems, failedItems) {
        this.deselectAllItems();
        this.discardActivePageItems();
        this.toggleBulkOperationStatusState(false);

        if (failedItems.length) {
            const modalTableTitle = Translator.trans(
                /*@Desc("%itemsCount% Location(s) cannot be added")*/
                'bulk_add_location.error.modal.table_title',
                { itemsCount: failedItems.length },
                'sub_items'
            );
            const notificationMessage = Translator.trans(
                /*@Desc("%failedCount% of the %totalCount% selected Locations(s) could not be added because you do not have proper user permissions. {{ moreInformationLink }} Contact your Administrator to obtain permissions.")*/
                'bulk_add_location.error.message',
                {
                    failedCount: failedItems.length,
                    totalCount: successItems.length + failedItems.length,
                },
                'sub_items'
            );
            const rawPlaceholdersMap = {
                moreInformationLink: Translator.trans(
                    /*@Desc("<u><a class='ez-notification-btn ez-notification-btn--show-modal'>Click here for more information.</a></u><br>")*/
                    'bulk_action.error.more_info',
                    {},
                    'sub_items'
                ),
            };

            this.handleBulkOperationFailedNotification(failedItems, modalTableTitle, notificationMessage, rawPlaceholdersMap);
        }

        if (successItems.length) {
            const message = Translator.trans(
                /*@Desc("Location(s) added to {{ locationLink }}.")*/
                'bulk_add_location.success.message',
                {},
                'sub_items'
            );
            const rawPlaceholdersMap = {
                locationLink: Translator.trans(
                    /*@Desc("<u><a href='%locationHref%'>%locationName%</a></u>")*/
                    'bulk_action.success.link_to_location',
                    {
                        locationName: eZ.helpers.text.escapeHTML(location.ContentInfo.Content.TranslatedName),
                        locationHref: this.props.generateLink(location.id, location.ContentInfo.id),
                    },
                    'sub_items'
                ),
            };

            window.eZ.helpers.notification.showSuccessNotification(message, () => {}, rawPlaceholdersMap);
        }
    }

    toggleUdw(show) {
        this.setState(() => ({
            isUdwOpened: show,
        }));
    }

    closeUdw() {
        this.toggleUdw(false);
    }

    onUdwConfirm([selectedLocation]) {
        this.closeUdw();
        const { actionFlow } = this.state;

        if (actionFlow === ACTION_FLOW_MOVE) {
            this.bulkMove(selectedLocation);
        } else {
            this.bulkAdd(selectedLocation);
        }
    }

    renderUdw() {
        const { isUdwOpened, actionFlow } = this.state;

        if (!isUdwOpened) {
            return null;
        }

        const UniversalDiscovery = window.eZ.modules.UniversalDiscovery;
        const { restInfo, parentLocationId, udwConfigBulkMoveItems, udwConfigBulkAddLocation } = this.props;
        const { selectedItems } = this.state;
        const selectedItemsLocationsIds = [...selectedItems.values()].map(({ id }) => id);
        const excludedLocations = [parentLocationId, ...selectedItemsLocationsIds];
        const title = Translator.trans(/*@Desc("Choose Location")*/ 'udw.choose_location.title', {}, 'sub_items');
        const udwConfig = actionFlow === ACTION_FLOW_MOVE ? udwConfigBulkMoveItems : udwConfigBulkAddLocation;
        const udwProps = {
            title,
            restInfo,
            onCancel: this.closeUdw,
            onConfirm: this.onUdwConfirm,
            canSelectContent: ({ item }, callback) => {
                callback(!excludedLocations.includes(item.id));
            },
            ...udwConfig,
        };

        return ReactDOM.createPortal(<UniversalDiscovery {...udwProps} />, this.udwContainer);
    }

    onDeleteBtnClick() {
        this.toggleBulkDeletePopup(true);
    }

    onHideBtnClick() {
        this.toggleBulkHidePopup(true);
    }

    onUnhideBtnClick() {
        this.toggleBulkUnhidePopup(true);
    }

    bulkDelete() {
        this.toggleBulkOperationStatusState(true);

        const { restInfo } = this.props;
        const { selectedItems } = this.state;
        const itemsToDelete = [...selectedItems.values()];

        bulkDeleteItems(restInfo, itemsToDelete, this.afterBulkDelete);
    }

    bulkHide() {
        this.toggleBulkOperationStatusState(true);

        const { restInfo } = this.props;
        const { selectedItems } = this.state;
        const items = [...selectedItems.values()];

        bulkHideLocations(restInfo, items, this.afterBulkHide);
    }

    bulkUnhide() {
        this.toggleBulkOperationStatusState(true);

        const { restInfo } = this.props;
        const { selectedItems } = this.state;
        const items = [...selectedItems.values()];

        bulkUnhideLocations(restInfo, items, this.afterBulkUnhide);
    }

    afterBulkDelete(deletedItems, notDeletedItems) {
        const { totalCount } = this.state;
        const isUser = ({ content }) => window.eZ.adminUiConfig.userContentTypes.includes(content._info.contentType.identifier);

        this.refreshContentTree();
        this.updateTotalCountState(totalCount - deletedItems.length);
        this.deselectAllItems();
        this.discardActivePageItems();
        this.updateTrashModal();

        this.toggleBulkOperationStatusState(false);

        if (notDeletedItems.length) {
            const hadUserContentItemFailed = notDeletedItems.some(isUser);
            const hadNonUserContentItemFailed = notDeletedItems.some((item) => !isUser(item));
            let modalTableTitle = null;
            let message = null;
            const rawPlaceholdersMap = {
                moreInformationLink: Translator.trans(
                    /*@Desc("<u><a class='ez-notification-btn ez-notification-btn--show-modal'>Click here for more information.</a></u><br>")*/
                    'bulk_action.error.more_info',
                    {},
                    'sub_items'
                ),
            };

            if (hadUserContentItemFailed && hadNonUserContentItemFailed) {
                modalTableTitle = Translator.trans(
                    /*@Desc("%itemsCount% Content item(s) cannot be deleted or sent to Trash")*/ 'bulk_delete.error.modal.table_title.users_with_nonusers',
                    {
                        itemsCount: notDeletedItems.length,
                    },
                    'sub_items'
                );
                message = Translator.trans(
                    /*@Desc("%notDeletedCount% of the %totalCount% selected item(s) could not be deleted or sent to Trash because you do not have proper user permissions. {{ moreInformationLink }} Contact your Administrator.")*/ 'bulk_delete.error.message.users_with_nonusers',
                    {
                        notDeletedCount: notDeletedItems.length,
                        totalCount: deletedItems.length + notDeletedItems.length,
                    },
                    'sub_items'
                );
            } else if (hadUserContentItemFailed) {
                modalTableTitle = Translator.trans(
                    /*@Desc("%itemsCount% User(s) cannot be deleted")*/ 'bulk_delete.error.modal.table_title.users',
                    {
                        itemsCount: notDeletedItems.length,
                    },
                    'sub_items'
                );
                message = Translator.trans(
                    /*@Desc("%notDeletedCount% of the %totalCount% selected item(s) could not be deleted because you do not have proper user permissions. {{ moreInformationLink }} Contact your Administrator.")*/ 'bulk_delete.error.message.users',
                    {
                        notDeletedCount: notDeletedItems.length,
                        totalCount: deletedItems.length + notDeletedItems.length,
                    },
                    'sub_items'
                );
            } else {
                modalTableTitle = Translator.trans(
                    /*@Desc("%itemsCount% Content item(s) cannot be sent to Trash")*/ 'bulk_delete.error.modal.table_title.nonusers',
                    {
                        itemsCount: notDeletedItems.length,
                    },
                    'sub_items'
                );
                message = Translator.trans(
                    /*@Desc("%notDeletedCount% of the %totalCount% selected item(s) could not be sent to Trash because you do not have proper user permissions. {{ moreInformationLink }} Contact your Administrator.")*/ 'bulk_delete.error.message.nonusers',
                    {
                        notDeletedCount: notDeletedItems.length,
                        totalCount: deletedItems.length + notDeletedItems.length,
                    },
                    'sub_items'
                );
            }

            this.handleBulkOperationFailedNotification(notDeletedItems, modalTableTitle, message, rawPlaceholdersMap);
        } else {
            const anyUserContentItemDeleted = deletedItems.some(isUser);
            const anyNonUserContentItemDeleted = deletedItems.some((location) => !isUser(location));
            let message = null;

            if (anyUserContentItemDeleted && anyNonUserContentItemDeleted) {
                message = Translator.trans(
                    /*@Desc("Content item(s) sent to Trash. User(s) deleted.")*/ 'bulk_delete.success.message.users_with_nonusers',
                    {},
                    'sub_items'
                );
            } else if (anyUserContentItemDeleted) {
                message = Translator.trans(/*@Desc("User(s) deleted.")*/ 'bulk_delete.success.message.users', {}, 'sub_items');
            } else {
                message = Translator.trans(
                    /*@Desc("Content item(s) sent to Trash.")*/ 'bulk_delete.success.message.nonusers',
                    {},
                    'sub_items'
                );
            }

            window.eZ.helpers.notification.showSuccessNotification(message);
        }
    }

    toggleBulkDeletePopup(show) {
        this.setState(() => ({
            isBulkDeletePopupVisible: show,
        }));
    }

    toggleBulkHidePopup(show) {
        this.setState(() => ({
            isBulkHidePopupVisible: show,
        }));
    }

    toggleBulkUnhidePopup(show) {
        this.setState(() => ({
            isBulkUnhidePopupVisible: show,
        }));
    }

    closeBulkDeletePopup() {
        this.toggleBulkDeletePopup(false);
    }

    closeBulkHidePopup() {
        this.toggleBulkHidePopup(false);
    }

    closeBulkUnhidePopup() {
        this.toggleBulkUnhidePopup(false);
    }

    onBulkDeletePopupConfirm() {
        this.closeBulkDeletePopup();
        this.bulkDelete();
    }

    onBulkHidePopupConfirm() {
        this.closeBulkHidePopup();
        this.bulkHide();
    }

    onBulkUnhidePopupConfirm() {
        this.closeBulkUnhidePopup();
        this.bulkUnhide();
    }

    /**
     * Shows warning notification which has a button.
     * Clicking the button should cause appearance of the modal
     * with list of items, which failed to be deleted/moved.
     *
     * @param {Array} failedItems
     * @param {String} modalTableTitle
     * @param {String} notificationMessage
     * @param {Object} rawPlaceholdersMap
     */
    handleBulkOperationFailedNotification(failedItems, modalTableTitle, notificationMessage, rawPlaceholdersMap) {
        const failedItemsData = failedItems.map(({ content }) => ({
            contentTypeName: content._info.contentType.name,
            contentName: content._name,
        }));

        window.eZ.helpers.notification.showWarningNotification(
            notificationMessage,
            (notificationNode) => {
                const showModalBtn = notificationNode.querySelector('.ez-notification-btn--show-modal');

                if (!showModalBtn) {
                    return;
                }

                showModalBtn.addEventListener('click', this.props.showBulkActionFailedModal.bind(null, modalTableTitle, failedItemsData));
            },
            rawPlaceholdersMap
        );
    }

    refreshContentTree() {
        document.body.dispatchEvent(new CustomEvent('ez-content-tree-refresh'));
    }

    renderDeleteConfirmationPopupFooter(selectionInfo) {
        const cancelLabel = Translator.trans(/*@Desc("Cancel")*/ 'bulk_action.popup.cancel', {}, 'sub_items');
        const { isUserContentItemSelected, isNonUserContentItemSelected } = selectionInfo;
        let confirmLabel = '';

        if (!isUserContentItemSelected && isNonUserContentItemSelected) {
            confirmLabel = Translator.trans(/*@Desc("Send to Trash")*/ 'bulk_delete.popup.confirm.nonusers', {}, 'sub_items');
        } else {
            confirmLabel = Translator.trans(/*@Desc("Delete")*/ 'bulk_delete.popup.confirm.users_and_users_with_nonusers', {}, 'sub_items');
        }

        return (
            <Fragment>
                <button
                    onClick={this.onBulkDeletePopupConfirm}
                    type="button"
                    className="btn ibexa-btn ibexa-btn--primary ibexa-btn--trigger">
                    {confirmLabel}
                </button>
                <button
                    onClick={this.closeBulkDeletePopup}
                    type="button"
                    className="btn ibexa-btn ibexa-btn--secondary"
                    data-bs-dismiss="modal">
                    {cancelLabel}
                </button>
            </Fragment>
        );
    }

    renderHideConfirmationPopupFooter() {
        const cancelLabel = Translator.trans(/*@Desc("Cancel")*/ 'bulk_action.popup.cancel', {}, 'sub_items');
        const confirmLabel = Translator.trans(/*@Desc("Hide")*/ 'bulk_hide.popup.confirm', {}, 'sub_items');

        return (
            <Fragment>
                <button onClick={this.onBulkHidePopupConfirm} type="button" className="btn ibexa-btn ibexa-btn--primary ibexa-btn--trigger">
                    {confirmLabel}
                </button>
                <button
                    onClick={this.closeBulkHidePopup}
                    type="button"
                    className="btn ibexa-btn ibexa-btn--secondary"
                    data-bs-dismiss="modal">
                    {cancelLabel}
                </button>
            </Fragment>
        );
    }

    renderUnhideConfirmationPopupFooter() {
        const cancelLabel = Translator.trans(/*@Desc("Cancel")*/ 'bulk_action.popup.cancel', {}, 'sub_items');
        const confirmLabel = Translator.trans(/*@Desc("Reveal")*/ 'bulk_unhide.popup.confirm', {}, 'sub_items');

        return (
            <Fragment>
                <button
                    onClick={this.onBulkUnhidePopupConfirm}
                    type="button"
                    className="btn ibexa-btn ibexa-btn--primary ibexa-btn--trigger">
                    {confirmLabel}
                </button>
                <button
                    onClick={this.closeBulkUnhidePopup}
                    type="button"
                    className="btn ibexa-btn ibexa-btn--secondary"
                    data-bs-dismiss="modal">
                    {cancelLabel}
                </button>
            </Fragment>
        );
    }

    getSelectionInfo() {
        const { selectedItems } = this.state;
        let isUserContentItemSelected = false;
        let isNonUserContentItemSelected = false;

        for (const [locationId, { content }] of selectedItems) {
            if (isUserContentItemSelected && isNonUserContentItemSelected) {
                break;
            }

            const isUserContentItem = window.eZ.adminUiConfig.userContentTypes.includes(content._info.contentType.identifier);

            if (isUserContentItem) {
                isUserContentItemSelected = true;
            } else {
                isNonUserContentItemSelected = true;
            }
        }

        return {
            isUserContentItemSelected,
            isNonUserContentItemSelected,
        };
    }

    renderDeleteConfirmationPopup() {
        const { isBulkDeletePopupVisible } = this.state;

        if (!isBulkDeletePopupVisible) {
            return null;
        }

        const confirmationMessageUsers = Translator.trans(
            /*@Desc("Are you sure you want to delete the selected user(s)?")*/ 'bulk_delete.popup.message.users',
            {},
            'sub_items'
        );
        const confirmationMessageNonUsers = Translator.trans(
            /*@Desc("Are you sure you want to send the selected Content item(s) to Trash?")*/ 'bulk_delete.popup.message.nonusers',
            {},
            'sub_items'
        );
        const confirmationMessageUsersAndNonUsers = Translator.trans(
            /*@Desc("Are you sure you want to delete the selected user(s) and send the other selected Content item(s) to Trash?")*/ 'bulk_delete.popup.message.users_with_nonusers',
            {},
            'sub_items'
        );
        const selectionInfo = this.getSelectionInfo();
        const { isUserContentItemSelected, isNonUserContentItemSelected } = selectionInfo;
        let confirmationMessage = '';

        if (isUserContentItemSelected && isNonUserContentItemSelected) {
            confirmationMessage = confirmationMessageUsersAndNonUsers;
        } else if (isUserContentItemSelected) {
            confirmationMessage = confirmationMessageUsers;
        } else {
            confirmationMessage = confirmationMessageNonUsers;
        }

        return ReactDOM.createPortal(
            <Popup
                onClose={this.closeBulkDeletePopup}
                isVisible={isBulkDeletePopupVisible}
                isLoading={false}
                size="medium"
                footerChildren={this.renderDeleteConfirmationPopupFooter(selectionInfo)}
                noHeader={true}>
                <div className="m-sub-items__confirmation-modal-body">{confirmationMessage}</div>
            </Popup>,
            this.bulkActionModalContainer
        );
    }

    renderHideConfirmationPopup() {
        const { isBulkHidePopupVisible } = this.state;

        if (!isBulkHidePopupVisible) {
            return null;
        }

        const confirmationMessage = Translator.trans(
            /*@Desc("Are you sure you want to hide the selected Location(s)?")*/
            'bulk_hide.popup.message',
            {},
            'sub_items'
        );

        return ReactDOM.createPortal(
            <Popup
                onClose={this.closeBulkHidePopup}
                isVisible={isBulkHidePopupVisible}
                isLoading={false}
                size="medium"
                footerChildren={this.renderHideConfirmationPopupFooter()}
                noHeader={true}>
                <div className="m-sub-items__confirmation-modal-body">{confirmationMessage}</div>
            </Popup>,
            this.bulkActionModalContainer
        );
    }

    renderUnhideConfirmationPopup() {
        const { isBulkUnhidePopupVisible } = this.state;

        if (!isBulkUnhidePopupVisible) {
            return null;
        }

        const confirmationMessage = Translator.trans(
            /*@Desc("Are you sure you want to reveal the selected Location(s)?")*/
            'bulk_unhide.popup.message',
            {},
            'sub_items'
        );

        return ReactDOM.createPortal(
            <Popup
                onClose={this.closeBulkUnhidePopup}
                isVisible={isBulkUnhidePopupVisible}
                isLoading={false}
                size="medium"
                footerChildren={this.renderUnhideConfirmationPopupFooter()}
                noHeader={true}>
                <div className="m-sub-items__confirmation-modal-body">{confirmationMessage}</div>
            </Popup>,
            this.bulkActionModalContainer
        );
    }

    changePage(pageIndex) {
        this.updateListViewHeight();
        this.setState(() => ({
            activePageIndex: pageIndex,
            activePageItems: null,
        }));
    }

    getPageSelectedLocationsIds() {
        const { selectedItems, activePageItems } = this.state;
        const selectedLocationsIds = [...selectedItems.keys()];
        const pageLocationsIds = [...activePageItems.map(({ id }) => id)];
        const selectedPageLocationsIds = new Set(pageLocationsIds.filter((locationId) => selectedLocationsIds.includes(locationId)));

        return selectedPageLocationsIds;
    }

    /**
     * Renders extra actions
     *
     * @method renderExtraActions
     * @param {Object} action
     * @returns {JSX.Element}
     * @memberof SubItemsModule
     */
    renderExtraActions(action, index) {
        const Action = action.component;

        return <Action key={index} className="m-sub-items__action" {...action.attrs} />;
    }

    /**
     * Renders pagination info,
     * which is information about how many items of all user is
     * viewing at the moment
     *
     * @method renderPaginationInfo
     * @returns {JSX.Element}
     */
    renderPaginationInfo() {
        const { totalCount, activePageItems } = this.state;
        const viewingCount = activePageItems ? activePageItems.length : 0;

        const message = Translator.trans(
            /*@Desc("Viewing %viewingCount% out of %totalCount% sub-items")*/ 'viewing_message',
            {
                viewingCount,
                totalCount,
            },
            'sub_items'
        );

        return <div className="m-sub-items__pagination-info ibexa-pagination__info" dangerouslySetInnerHTML={{ __html: message }} />;
    }

    /**
     * Renders pagination
     *
     * @method renderPagination
     * @returns {JSX.Element|null}
     * @memberof SubItemsModule
     */
    renderPagination() {
        const { limit: itemsPerPage } = this.props;
        const { totalCount } = this.state;
        const { activePageIndex, activePageItems, isDuringBulkOperation } = this.state;
        const isActivePageLoaded = !!activePageItems;
        const isPaginationDisabled = !isActivePageLoaded || isDuringBulkOperation;

        return (
            <Pagination
                proximity={1}
                itemsPerPage={itemsPerPage}
                activePageIndex={activePageIndex}
                totalCount={totalCount}
                onPageChange={this.changePage}
                disabled={isPaginationDisabled}
            />
        );
    }

    renderBulkMoveBtn(disabled) {
        const label = Translator.trans(/*@Desc("Move")*/ 'move_btn.label', {}, 'sub_items');

        return <ActionButton disabled={disabled} onClick={this.onMoveBtnClick} label={label} type="move" />;
    }

    renderBulkAddLocationBtn(disabled) {
        const label = Translator.trans(/*@Desc("Add Locations")*/ 'add_locations_btn.label', {}, 'sub_items');

        return (
            <ActionButton disabled={disabled} onClick={this.onAddLocationsBtnClick} label={label} type="create-location" />
        );
    }

    renderBulkHideBtn(disabled) {
        const label = Translator.trans(/*@Desc("Hide")*/ 'hide_locations_btn.label', {}, 'sub_items');

        return <ActionButton disabled={disabled} onClick={this.onHideBtnClick} label={label} type="hide" />;
    }

    renderBulkUnhideBtn(disabled) {
        const label = Translator.trans(/*@Desc("Reveal")*/ 'unhide_locations_btn.label', {}, 'sub_items');

        return <ActionButton disabled={disabled} onClick={this.onUnhideBtnClick} label={label} type="reveal" />;
    }

    renderBulkDeleteBtn(disabled) {
        const label = Translator.trans(/*@Desc("Delete")*/ 'trash_btn.label', {}, 'sub_items');

        return <ActionButton disabled={disabled} onClick={this.onDeleteBtnClick} label={label} type="trash" />;
    }

    renderSpinner() {
        const { activePageItems } = this.state;
        const isActivePageLoaded = !!activePageItems;

        if (isActivePageLoaded) {
            return null;
        }

        const { listViewHeight } = this.state;
        const spinnerMinHeight = 90;
        const style = {
            height: listViewHeight && listViewHeight > spinnerMinHeight ? listViewHeight : spinnerMinHeight,
        };

        return (
            <div style={style}>
                <div className="m-sub-items__spinner-wrapper">
                    <Icon name="spinner" extraClasses="m-sub-items__spinner ibexa-icon--medium ibexa-spin" />
                </div>
            </div>
        );
    }

    renderNoItems() {
        if (this.state.totalCount) {
            return null;
        }

        return <NoItemsComponent />;
    }

    renderListView() {
        const { activePageItems, sortClause, sortOrder } = this.state;
        const pageLoaded = !!activePageItems;

        if (!pageLoaded) {
            return null;
        }

        const selectedPageLocationsIds = this.getPageSelectedLocationsIds();

        return (
            <SubItemsListComponent
                activeView={this.state.activeView}
                handleItemPriorityUpdate={this.handleItemPriorityUpdate}
                items={activePageItems}
                languages={this.props.languages}
                handleEditItem={this.props.handleEditItem}
                generateLink={this.props.generateLink}
                onItemSelect={this.toggleItemSelection}
                toggleAllItemsSelect={this.toggleAllPageItemsSelection}
                selectedLocationsIds={selectedPageLocationsIds}
                onSortChange={this.changeSorting}
                sortClause={sortClause}
                sortOrder={sortOrder}
                languageContainerSelector={this.props.languageContainerSelector}
            />
        );
    }

    updateTrashModal() {
        document.body.dispatchEvent(
            new CustomEvent('ez-trash-modal-refresh', {
                detail: {
                    numberOfSubitems: this.state.totalCount,
                },
            })
        );
    }

    render() {
        const listTitle = Translator.trans(/*@Desc("Sub-items")*/ 'items_list.title', {}, 'sub_items');
        const { selectedItems, activeView, totalCount, isDuringBulkOperation, activePageItems, subItemsWidth } = this.state;
        const nothingSelected = !selectedItems.size;
        const isTableViewActive = activeView === VIEW_MODE_TABLE;
        const pageLoaded = !!activePageItems;
        const bulkBtnDisabled = nothingSelected || !isTableViewActive || !pageLoaded;
        let bulkHideBtnDisabled = true;
        let bulkUnhideBtnDisabled = true;
        let listClassName = 'm-sub-items__list';

        if (isDuringBulkOperation) {
            listClassName = `${listClassName} ${listClassName}--processing`;
        }

        if (!bulkBtnDisabled) {
            const selectedItemsValues = [...selectedItems.values()];

            bulkHideBtnDisabled = !selectedItemsValues.some((item) => !item.hidden);
            bulkUnhideBtnDisabled = !selectedItemsValues.some((item) => !!item.hidden);
        }

        return (
            <div className="m-sub-items" style={{ width: `${subItemsWidth}px` }}>
                <div className="ibexa-table-header ">
                    <div className="ibexa-table-header__headline">
                        {listTitle} ({this.state.totalCount})
                    </div>
                    <div className="ibexa-table-header__actions">
                        {this.props.extraActions.map(this.renderExtraActions)}
                        {this.renderBulkMoveBtn(bulkBtnDisabled)}
                        {this.renderBulkAddLocationBtn(bulkBtnDisabled)}
                        {this.renderBulkHideBtn(bulkHideBtnDisabled)}
                        {this.renderBulkUnhideBtn(bulkUnhideBtnDisabled)}
                        {this.renderBulkDeleteBtn(bulkBtnDisabled)}
                        <ViewSwitcherComponent onViewChange={this.switchView} activeView={activeView} isDisabled={!totalCount} />
                    </div>
                </div>
                <div ref={this._refListViewWrapper} className={listClassName}>
                    {this.renderSpinner()}
                    {this.renderListView()}
                    {this.renderNoItems()}
                </div>
                <div className="m-sub-items__pagination-container ibexa-pagination">
                    {this.renderPaginationInfo()}
                    {this.renderPagination()}
                </div>
                {this.renderUdw()}
                {this.renderDeleteConfirmationPopup()}
                {this.renderHideConfirmationPopup()}
                {this.renderUnhideConfirmationPopup()}
            </div>
        );
    }
}

eZ.addConfig('modules.SubItems', SubItemsModule);

SubItemsModule.propTypes = {
    parentLocationId: PropTypes.number.isRequired,
    restInfo: PropTypes.shape({
        token: PropTypes.string.isRequired,
        siteaccess: PropTypes.string.isRequired,
    }).isRequired,
    loadLocation: PropTypes.func,
    sortClauses: PropTypes.object,
    updateLocationPriority: PropTypes.func,
    activeView: PropTypes.string,
    extraActions: PropTypes.arrayOf(
        PropTypes.shape({
            component: PropTypes.func,
            attrs: PropTypes.object,
        })
    ),
    items: PropTypes.arrayOf(PropTypes.object),
    limit: PropTypes.number,
    offset: PropTypes.number,
    handleEditItem: PropTypes.func.isRequired,
    generateLink: PropTypes.func.isRequired,
    totalCount: PropTypes.number,
    languages: PropTypes.object,
    udwConfigBulkMoveItems: PropTypes.object.isRequired,
    udwConfigBulkAddLocation: PropTypes.object.isRequired,
    showBulkActionFailedModal: PropTypes.func.isRequired,
    languageContainerSelector: PropTypes.string,
};

SubItemsModule.defaultProps = {
    loadLocation,
    sortClauses: {},
    updateLocationPriority,
    activeView: VIEW_MODE_TABLE,
    extraActions: [],
    languages: window.eZ.adminUiConfig.languages,
    items: [],
    limit: parseInt(window.eZ.adminUiConfig.subItems.limit, 10),
    offset: 0,
    totalCount: 0,
    languageContainerSelector: '.ibexa-extra-actions-container',
};
