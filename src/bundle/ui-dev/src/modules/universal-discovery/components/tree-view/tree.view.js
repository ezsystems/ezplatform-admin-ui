import React, { useContext, useMemo } from 'react';
import PropTypes from 'prop-types';

import ContentTreeModule from '../../../content-tree/content.tree.module';
import { findLocationsById } from '../../services/universal.discovery.service';
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
} from '../../universal.discovery.module';

const flattenTree = (tree) => tree.reduce((output, branch) => [...output, branch.locationId, ...flattenTree(branch.subitems)], []);

const TreeView = () => {
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useContext(LoadedLocationsMapContext);
    const [markedLocationId, setMarkedLocationId] = useContext(MarkedLocationIdContext);
    const [multiple, multipleItemsLimit] = useContext(MultipleConfigContext);
    const allowedContentTypes = useContext(AllowedContentTypesContext);
    const containersOnly = useContext(ContainersOnlyContext);
    const contentTypesMap = useContext(ContentTypesMapContext);
    const restInfo = useContext(RestInfoContext);
    const rootLocationId = useContext(RootLocationIdContext);
    const locationData = useMemo(() => getLocationData(loadedLocationsMap, markedLocationId), [markedLocationId, loadedLocationsMap]);
    const markLocation = (item, event) => {
        console.log(loadedLocationsMap);
        event.preventDefault();

        const { locationId } = item;

        if (locationId === markedLocationId) {
            return;
        }
    
        setMarkedLocationId(locationId);
        dispatchLoadedLocationsAction({ type: 'CUT_LOCATIONS', locationId: markedLocationId });
        dispatchLoadedLocationsAction({ type: 'UPDATE_LOCATIONS', data: { parentLocationId: locationId, subitems: [] } });

        console.log({ ...restInfo, id: locationId });

        findLocationsById({ ...restInfo, id: locationId }, ([location]) => {
            console.log(location);
            const contentTypeInfo = contentTypesMap[location.ContentInfo.Content.ContentType._href];
            const isContainer = contentTypeInfo.isContainer;
            const isNotSelectable =
                (containersOnly && !isContainer) || (allowedContentTypes && !allowedContentTypes.includes(contentTypeInfo.identifier));
            
            // if (!multiple && !isNotSelectable) {
                dispatchSelectedLocationsAction({ type: 'CLEAR_SELECTED_LOCATIONS' });
                dispatchSelectedLocationsAction({ type: 'ADD_SELECTED_LOCATION', location });
            // }
        });
    }
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
    }
    const currentLocationPath = locationData ? locationData.location.pathString : '/1/2/'; // TODO: Get default path

    return (
        <div className="c-tree">
            <ContentTreeModule 
                userId={14}
                currentLocationPath={currentLocationPath}
                rootLocationId={rootLocationId}
                restInfo={restInfo}
                onClickItem={markLocation}
                readSubtree={readSubtree}
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
