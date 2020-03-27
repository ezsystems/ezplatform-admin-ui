import React, { useContext, useEffect, useRef } from 'react';
import PropTypes from 'prop-types';

import FinderBranch from './finder.branch';

import { LoadedLocationsMapContext } from '../../universal.discovery.module';

const Finder = ({ itemsPerPage }) => {
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useContext(LoadedLocationsMapContext);
    const finderRef = useRef(null);

    useEffect(() => {
        const container = finderRef.current;

        container.scrollLeft = container.scrollWidth - container.clientWidth;
    });

    return (
        <div className="c-finder" ref={finderRef}>
            {loadedLocationsMap.map((loadedLocation) => (
                <FinderBranch key={loadedLocation.parentLocationId} itemsPerPage={itemsPerPage} locationData={loadedLocation} />
            ))}
        </div>
    );
};

Finder.propTypes = {
    itemsPerPage: PropTypes.number,
};

Finder.defaultProps = {
    itemsPerPage: 50,
};

export default Finder;
