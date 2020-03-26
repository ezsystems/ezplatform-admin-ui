import React from 'react';
import PropTypes from 'prop-types';
import ListItem from '../list-item/list.item.component';

const List = ({ items, loadMoreSubitems, currentLocationId, path, subitemsLoadLimit, subitemsLimit, treeMaxDepth, afterItemToggle, isRoot }) => {
    const commonAttrs = { loadMoreSubitems, subitemsLoadLimit, subitemsLimit, treeMaxDepth, afterItemToggle };
    const listAttrs = { ...commonAttrs, currentLocationId };
    const listItemAttrs = commonAttrs;

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
                        path={itemPath}>
                        {subitems.length ? <List path={itemPath} items={subitems} isRoot={false} {...listAttrs} /> : null}
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
};

List.defaultProps = {
    isRoot: false,
};

export default List;
