import React, { Component, useState } from 'react';
import PropTypes from 'prop-types';
import ContentTree from './components/content-tree/content.tree';
import { loadLocationItems, loadSubtree } from './services/content.tree.service';
import { INSERT_AFTER } from '../../../../../../../../ibexa/tree-builder/src/bundle/Resources/public/js/tree.builder.module';

const KEY_CONTENT_TREE_SUBTREE = 'ez-content-tree-subtrees';

export default class ContentTreeModule extends Component {
    constructor(props) {
        super(props);

        this.setInitialItemsState = this.setInitialItemsState.bind(this);
        this.loadMoreSubitems = this.loadMoreSubitems.bind(this);
        this.updateSubtreeAfterItemToggle = this.updateSubtreeAfterItemToggle.bind(this);
        this.handleCollapseAllItems = this.handleCollapseAllItems.bind(this);
        this.limitSubitemsInSubtree = this.limitSubitemsInSubtree.bind(this);
        this.refreshContentTree = this.refreshContentTree.bind(this);
        this.getLoadSubtreeParams = this.getLoadSubtreeParams.bind(this);

        try {
            const savedSubtree = this.readSubtree();

            this.items = props.preloadedLocations;
            this.subtree = savedSubtree ? savedSubtree : this.generateInitialSubtree();

            this.expandCurrentLocationInSubtree();
            this.clipTooDeepSubtreeBranches(this.subtree[0], props.treeMaxDepth - 1);
            this.subtree[0].children.forEach(this.limitSubitemsInSubtree);
            this.saveSubtree();
        } catch (error) {
            this.items = [];
            this.subtree = this.generateInitialSubtree();
            this.saveSubtree();
        }
    }

    componentDidMount() {
        document.body.addEventListener('ez-content-tree-refresh', this.refreshContentTree, false);

        if (this.items.length) {
            this.subtree = this.generateSubtree(this.items, true);
            this.saveSubtree();

            return;
        }

        loadSubtree(this.getLoadSubtreeParams(), (loadedSubtree) => {
            this.setInitialItemsState(loadedSubtree[0]);
        });
    }

    componentDidUpdate(prevProps) {
        if (prevProps.sort.sortClause !== this.props.sort.sortClause || prevProps.sort.sortOrder !== this.props.sort.sortOrder) {
            loadSubtree(this.getLoadSubtreeParams(), (loadedSubtree) => {
                this.setInitialItemsState(loadedSubtree[0]);
            });
        }
    }

    setInitialItemsState(location) {
        this.items = [location];
        this.subtree = this.generateSubtree(this.items, true);

        this.saveSubtree();
        this.forceUpdate();
    }

    loadMoreSubitems({ parentLocationId, offset, limit, path }, successCallback) {
        loadLocationItems(
            this.props.restInfo,
            parentLocationId,
            this.updateLocationsStateAfterLoadingMoreItems.bind(this, path, successCallback),
            limit,
            offset
        );
    }

    refreshContentTree() {
        this.items = [];
        this.forceUpdate();

        loadSubtree(this.getLoadSubtreeParams(), (loadedSubtree) => {
            this.setInitialItemsState(loadedSubtree[0]);
        });
    }

    updateLocationsStateAfterLoadingMoreItems(path, successCallback, location) {
        const item = this.findItem(this.items, path.split(','));

        if (!item) {
            return;
        }

        item.subitems = [...item.subitems, ...location.subitems];

        this.updateSubtreeAfterLoadMoreItems(path);
        successCallback();
        this.forceUpdate();
    }

    updateSubtreeAfterLoadMoreItems(path) {
        const item = this.findItem(this.items, path.split(','));

        this.updateItemInSubtree(this.subtree[0], item, path.split(','));
        this.saveSubtree();
    }

    updateSubtreeAfterItemToggle(path, isExpanded) {
        const item = this.findItem(this.items, path.split(','));

        if (isExpanded) {
            this.addItemToSubtree(this.subtree[0], item, path.split(','));
        } else {
            this.removeItemFromSubtree(this.subtree[0], item, path.split(','));
        }

        this.saveSubtree();
        this.props.afterItemToggle(item, isExpanded);
    }

    addItemToSubtree(subtree, item, path) {
        const parentSubtree = this.findParentSubtree(subtree, path);

        if (!parentSubtree) {
            return;
        }

        const { subitemsLoadLimit, subitemsLimit } = this.props;
        const limit = Math.ceil(item.subitems.length / subitemsLoadLimit) * subitemsLoadLimit;

        parentSubtree.children.push({
            '_media-type': 'application/vnd.ez.api.ContentTreeLoadSubtreeRequestNode',
            locationId: item.locationId,
            limit: Math.min(subitemsLimit, limit),
            offset: 0,
            children: [],
        });
    }

    removeItemFromSubtree(subtree, item, path) {
        const parentSubtree = this.findParentSubtree(subtree, path);

        if (!parentSubtree) {
            return;
        }

        const index = parentSubtree.children.findIndex((element) => element.locationId === item.locationId);

        if (index > -1) {
            parentSubtree.children.splice(index, 1);
        }
    }

    updateItemInSubtree(subtree, item, path) {
        const parentSubtree = this.findParentSubtree(subtree, path);

        if (!parentSubtree) {
            return;
        }

        const index = parentSubtree.children.findIndex((element) => element.locationId === item.locationId);

        if (index > -1) {
            parentSubtree.children[index].limit = item.subitems.length;
        }
    }

    readSubtree() {
        const { readSubtree } = this.props;

        if (typeof readSubtree === 'function') {
            return readSubtree();
        }

        const { rootLocationId, userId } = this.props;
        const savedSubtrees = localStorage.getItem(KEY_CONTENT_TREE_SUBTREE);
        const subtrees = savedSubtrees ? JSON.parse(savedSubtrees) : null;
        const userSubtrees = subtrees ? subtrees[userId] : null;
        const savedSubtree = userSubtrees ? userSubtrees[rootLocationId] : null;
        const subtree = savedSubtree ? JSON.parse(savedSubtree) : null;

        return subtree;
    }

    saveSubtree() {
        const { rootLocationId, userId } = this.props;
        const savedSubtreesStringified = localStorage.getItem(KEY_CONTENT_TREE_SUBTREE);
        const subtrees = savedSubtreesStringified ? JSON.parse(savedSubtreesStringified) : {};

        if (!subtrees[userId]) {
            subtrees[userId] = {};
        }

        subtrees[userId][rootLocationId] = JSON.stringify(this.subtree);

        localStorage.setItem(KEY_CONTENT_TREE_SUBTREE, JSON.stringify(subtrees));
    }

    findParentSubtree(subtree, path) {
        if (path.length < 2) {
            return;
        }

        path.shift();
        path.pop();

        return path.reduce(
            (subtreeChild, locationId) => subtreeChild.children.find((element) => element.locationId === parseInt(locationId, 10)),
            subtree
        );
    }

    expandCurrentLocationInSubtree() {
        const { rootLocationId, currentLocationPath } = this.props;
        const path = currentLocationPath.split('/').filter((id) => !!id);
        const rootLocationIdIndex = path.findIndex((element) => parseInt(element, 10) === rootLocationId);

        if (rootLocationIdIndex === -1) {
            return;
        }

        const pathStartingAfterRootLocation = path.slice(rootLocationIdIndex - path.length + 1);
        const pathWithoutLeaf = pathStartingAfterRootLocation.slice(0, pathStartingAfterRootLocation.length - 1);

        this.expandPathInSubtree(this.subtree[0], pathWithoutLeaf);
    }

    expandPathInSubtree(subtree, path) {
        if (!path.length) {
            return;
        }

        const locationId = parseInt(path[0], 10);
        let nextSubtree = subtree.children.find((element) => element.locationId === locationId);

        if (!nextSubtree) {
            nextSubtree = {
                '_media-type': 'application/vnd.ez.api.ContentTreeLoadSubtreeRequestNode',
                locationId: locationId,
                limit: this.props.subitemsLimit,
                offset: 0,
                children: [],
            };
            subtree.children.push(nextSubtree);
        }

        path.shift();
        this.expandPathInSubtree(nextSubtree, path);
    }

    clipTooDeepSubtreeBranches(subtree, maxDepth) {
        if (maxDepth <= 0) {
            subtree.children = [];

            return;
        }

        subtree.children.forEach((subtreeChild) => this.clipTooDeepSubtreeBranches(subtreeChild, maxDepth - 1));
    }

    limitSubitemsInSubtree(subtree) {
        subtree.limit = Math.min(this.props.subitemsLimit, subtree.limit);
        subtree.children.forEach(this.limitSubitemsInSubtree);
    }

    generateInitialSubtree() {
        return [
            {
                '_media-type': 'application/vnd.ez.api.ContentTreeLoadSubtreeRequestNode',
                locationId: this.props.rootLocationId,
                limit: this.props.subitemsLoadLimit,
                offset: 0,
                children: [],
            },
        ];
    }

    generateSubtree(items, isRoot) {
        const itemsWithoutLeafs = [];
        const { subitemsLoadLimit, subitemsLimit } = this.props;

        for (const item of items) {
            const subitemsCount = item.subitems.length;
            const isLeaf = !subitemsCount;

            if (!isLeaf || isRoot) {
                const limit = subitemsCount ? Math.ceil(subitemsCount / subitemsLoadLimit) * subitemsLoadLimit : subitemsLoadLimit;

                itemsWithoutLeafs.push({
                    '_media-type': 'application/vnd.ez.api.ContentTreeLoadSubtreeRequestNode',
                    locationId: item.locationId,
                    limit: Math.min(subitemsLimit, limit),
                    offset: 0,
                    children: this.generateSubtree(item.subitems, false),
                });
            }
        }

        return itemsWithoutLeafs;
    }

    findItem(items, path) {
        const isLast = path.length === 1;
        const item = items.find((element) => element.locationId === parseInt(path[0], 10));

        if (!item) {
            return null;
        }

        if (isLast) {
            return item;
        }

        if (!(item.hasOwnProperty('subitems') && Array.isArray(item.subitems))) {
            return null;
        }

        path.shift();

        return this.findItem(item.subitems, path);
    }

    getCurrentLocationId() {
        const currentLocationIdString = this.props.currentLocationPath
            .split('/')
            .filter((id) => !!id)
            .pop();

        return parseInt(currentLocationIdString, 10);
    }

    handleCollapseAllItems() {
        this.items = [];
        this.forceUpdate();

        this.subtree = this.generateInitialSubtree();
        this.saveSubtree();

        loadSubtree(this.getLoadSubtreeParams(), (loadedSubtree) => {
            this.setInitialItemsState(loadedSubtree[0]);
        });
    }

    getLoadSubtreeParams() {
        return {
            token: this.props.restInfo.token,
            siteaccess: this.props.restInfo.siteaccess,
            subtree: this.subtree,
            sortClause: this.props.sort.sortClause,
            sortOrder: this.props.sort.sortOrder,
        };
    }

    render() {
        const { onClickItem, subitemsLimit, subitemsLoadLimit, treeMaxDepth, userId } = this.props;
        const attrs = {
            items: this.items,
            currentLocationId: this.getCurrentLocationId(),
            subitemsLimit,
            subitemsLoadLimit,
            treeMaxDepth,
            loadMoreSubitems: this.loadMoreSubitems,
            afterItemToggle: this.updateSubtreeAfterItemToggle,
            onCollapseAllItems: this.handleCollapseAllItems,
            onClickItem,
            userId,
        };

        return <ContentTree {...attrs} />;
    }
}

const TreeBuilderModule = (props) => {
    const [mockedTree, setMockedTree] = useState({
        id: 0,
        name: 'Root',
        isRootItem: true,
        total: 3,
        children: [
            {
                id: 1,
                name: 'Jeden',
                total: 20,
                children: [
                    {
                        id: 11,
                        name: 'Jedenaście',
                    },
                    {
                        id: 12,
                        name: 'Dwanaście',
                    },
                    {
                        id: 13,
                        name: 'Trzynaście',
                    },
                ],
            },
            {
                id: 2,
                name: 'Dwa',
                total: 0,
                children: [],
            },
            {
                id: 3,
                name: 'Trzy',
                total: 3,
                children: [
                    {
                        id: 31,
                        name: 'Trzydzieści jeden',
                        total: 2,
                        children: [
                            {
                                id: 311,
                                name: 'Trzysta jedenaście',
                            },
                            {
                                id: 312,
                                name: 'Trzysta dwanaście',
                            },
                        ],
                    },
                    {
                        id: 32,
                        name: 'Trzydzieści dwa',
                        isInvisible: true,
                        total: 0,
                    },
                    {
                        id: 33,
                        name: 'Trzydzieści trzy',
                        total: 1,
                        children: [
                            {
                                id: 331,
                                name: 'Trzysta trzydzieści jeden',
                                total: 1,
                                children: [
                                    {
                                        id: 3311,
                                        name: 'Trzy tysiące trzysta jedenaście',
                                    },
                                ],
                            },
                        ],
                    },
                ],
            },
        ],
    });
    const findHelper = (element, id) => {
        if (element.id === id) {
            throw element;
        } else if (element.children) {
            element.children.forEach((el) => findHelper(el, id));
        }
    };
    const findParentHelper = (element, id) => {
        if (element.children) {
            if (element.children.find((el) => el.id === id)) {
                throw element;
            }
            element.children.forEach((el) => findParentHelper(el, id));
        }
    };
    const loadMoreSubitems = ({ item }) => {
        return new Promise((resolve) => {
            setTimeout(() => {
                const newTree = JSON.parse(JSON.stringify(mockedTree));
                let element;
                try {
                    findHelper(newTree, item.id);
                } catch (e) {
                    element = e;
                }
                element.children = [...element.children, ...element.children].slice(0, element.total);
                setMockedTree(newTree);
                resolve();
            }, 500);
        });
    };
    const isSelected = (data) => {
        return data.id === 1;
    };
    const moveElement = ({ data, parent, insertIndex }) => {
        return new Promise((resolve) => {
            setTimeout(() => {
                const newTree = JSON.parse(JSON.stringify(mockedTree));
                let parentElement;
                let movedElement;
                let parentMovedElement;
                try {
                    findHelper(newTree, parent.id);
                } catch (e) {
                    parentElement = e;
                }
                try {
                    findHelper(newTree, data.id);
                } catch (e) {
                    movedElement = e;
                }
                try {
                    findParentHelper(newTree, data.id);
                } catch (e) {
                    parentMovedElement = e;
                }
                parentElement.children.splice(insertIndex, 0, movedElement);
                const movedIndex = parentMovedElement.children.findIndex((el) => el.id === data.id);
                parentMovedElement.children.splice(movedIndex, 1);
                setMockedTree(newTree);
                resolve();
            }, 500);
        });
    };
    const customItemClass = (data) => {
        const { children, total, isInvisible, isRootItem } = data;
        const itemClassName = 't-list-item';
        let className = '';
        if (children && children.length < total) {
            className += ` ${itemClassName}--can-load-more`;
        }
        if (isInvisible) {
            className += ` ${itemClassName}--is-hidden`;
        }
        if (isRootItem) {
            className += ` ${itemClassName}--is-root-item`;
        }
        return className;
    };

    return (
        <eZ.modules.TreeBuilder
            tree={mockedTree}
            getSubitems={(data) => data.children}
            getLabel={(data) => data.name}
            getTotal={(data) => data.total}
            getId={(data) => data.id}
            isSelected={isSelected}
            loadMoreSubitems={loadMoreSubitems}
            moveElement={moveElement}
            customItemClass={customItemClass}
            subitemsLimit={10}
            treeDepthLimit={4}
            moduleId="taxonomyTree"
            moduleName="Taxonomy tree"
            // dragDisabled={true}
        />
    );
};

window.ibexa = {
    treeBuilder: {
        taxonomyTree: {
            actions: [],
        },
    },
};

eZ.addConfig('modules.ContentTree', ContentTreeModule);
eZ.addConfig('modules.ContentTree', TreeBuilderModule);

ContentTreeModule.propTypes = {
    rootLocationId: PropTypes.number.isRequired,
    currentLocationPath: PropTypes.number.isRequired,
    userId: PropTypes.number.isRequired,
    preloadedLocations: PropTypes.arrayOf(PropTypes.object),
    subitemsLimit: PropTypes.number.isRequired,
    subitemsLoadLimit: PropTypes.number.isRequired,
    treeMaxDepth: PropTypes.number.isRequired,
    restInfo: PropTypes.shape({
        token: PropTypes.string.isRequired,
        siteaccess: PropTypes.string.isRequired,
    }).isRequired,
    onClickItem: PropTypes.func,
    readSubtree: PropTypes.func,
    afterItemToggle: PropTypes.func,
    sort: PropTypes.shape({
        sortClause: PropTypes.string,
        sortOrder: PropTypes.string,
    }),
};

ContentTreeModule.defaultProps = {
    preloadedLocations: [],
    rootLocationId: window.eZ.adminUiConfig.contentTree.treeRootLocationId,
    subitemsLimit: window.eZ.adminUiConfig.contentTree.childrenLoadMaxLimit,
    subitemsLoadLimit: window.eZ.adminUiConfig.contentTree.loadMoreLimit,
    treeMaxDepth: window.eZ.adminUiConfig.contentTree.treeMaxDepth,
    afterItemToggle: () => {},
    sort: {},
};
