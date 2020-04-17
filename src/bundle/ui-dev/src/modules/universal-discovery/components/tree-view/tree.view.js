import React, { useContext, useMemo, useState, useEffect } from 'react';
import PropTypes from 'prop-types';

import ContentTreeModule from '../../../content-tree/content.tree.module';
import {
    RootLocationIdContext, 
    RestInfoContext, 
} from '../../universal.discovery.module';

const flattenTree = (tree) => tree.reduce((output, branch) => [...output, branch.locationId, ...flattenTree(branch.subitems)], []);

const onClickTreeItem = (event) => {
    event.preventDefault();

    event.currentTarget.closest('.c-list-item__label').querySelector('.c-toggle-selection-button').click();
}

const TreeView = () => {
    const rootLocationId = useContext(RootLocationIdContext);
    const restInfo = useContext(RestInfoContext);

    return (
        <div className="c-tree">
            <ContentTreeModule 
                userId={14}
                currentLocationPath="/1/2/"
                rootLocationId={rootLocationId}
                restInfo={restInfo}
                onClickItem={onClickTreeItem}
            />
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
