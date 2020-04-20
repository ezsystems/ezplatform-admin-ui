import React from 'react';
import PropTypes from 'prop-types';
import ListItem from '../list-item/list.item.component';

const List = ({
    items,
    loadMoreSubitems,
    currentLocationId,
    path,
    subitemsLoadLimit,
    subitemsLimit,
    treeMaxDepth,
    afterItemToggle,
    isRoot,
    onClickItem,
}) => {
    const commonAttrs = { loadMoreSubitems, subitemsLoadLimit, subitemsLimit, treeMaxDepth, afterItemToggle, onClickItem };
    const listAttrs = { ...commonAttrs, currentLocationId };
    const listItemAttrs = commonAttrs;
    const renderNoSubitemMessage = () => {
        const rootLocation = items[0];
        const isRootLoaded = rootLocation;
        const noSubitemsMessage = Translator.trans(/*@Desc("This Location has no sub-items")*/ 'no_subitems', {}, 'content_tree');

        if (!isRoot || !isRootLoaded || (rootLocation.subitems && rootLocation.subitems.length)) {
            return;
        }

        return <div className="c-list__no-items-message">{noSubitemsMessage}</div>;
    };

    return (
        <ul className="c-list">
            {items.map((item) => {
                const hasPreviousPath = path && path.length;
                const locationHref = window.Routing.generate('_ez_content_view', {
                    contentId: item.contentId,
                    locationId: item.locationId,
                });
                const itemPath = `${hasPreviousPath ? path + ',' : ''}${item.locationId}`;
                const { subitems } = item;

                return (
                    <ListItem
                        {...item}
                        {...listItemAttrs}
                        key={item.locationId}
                        selected={item.locationId === currentLocationId}
                        href={locationHref}
                        isRootItem={isRoot}
                        onClick={onClickItem.bind(null, item)}
                        path={itemPath}>
                        {subitems.length ? (
                            <List path={itemPath} items={subitems} isRoot={false} {...listAttrs} />
                        ) : (
                            renderNoSubitemMessage()
                        )}
                    </ListItem>
                );
            })}
        </ul>
    );
};

List.propTypes = {
    path: PropTypes.string.isRequired,
    items: PropTypes.array.isRequired,
    loadMoreSubitems: PropTypes.func.isRequired,
    currentLocationId: PropTypes.number.isRequired,
    subitemsLimit: PropTypes.number.isRequired,
    subitemsLoadLimit: PropTypes.number,
    treeMaxDepth: PropTypes.number.isRequired,
    afterItemToggle: PropTypes.func.isRequired,
    isRoot: PropTypes.bool.isRequired,
    onClickItem: PropTypes.func,
};

List.defaultProps = {
    isRoot: false,
};

export default List;
