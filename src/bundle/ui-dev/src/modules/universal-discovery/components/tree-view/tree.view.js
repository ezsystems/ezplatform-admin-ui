import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import ContentTreeModule from '../../../content-tree/content.tree.module';
import { 
    RootLocationIdContext, 
    RestInfoContext 
} from '../../universal.discovery.module';

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
