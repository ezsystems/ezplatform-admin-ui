import { useEffect, useContext, useReducer } from 'react';

import { findLocationsByParentLocationId } from '../services/universal.discovery.service';
import { RestInfoContext } from '../universal.discovery.module';

const fetchInitialState = {
    dataFetched: false,
    data: {},
};

const fetchReducer = (state, action) => {
    switch (action.type) {
        case 'FETCH_START':
            return fetchInitialState;
        case 'FETCH_END':
            return { data: action.data, dataFetched: true };
        default:
            throw new Error();
    }
};

export const useFindLocationsByParentLocationIdFetch = (locationData, { sortClause, sortOrder }, limit, offset, gridView = false) => {
    const restInfo = useContext(RestInfoContext);
    const [state, dispatch] = useReducer(fetchReducer, fetchInitialState);

    useEffect(() => {
        let effectCleaned = false;

        if (
            !locationData.parentLocationId ||
            locationData.collapsed ||
            locationData.subitems.length >= locationData.totalCount || locationData.subitems.length >= limit + offset
        ) {
            dispatch({ type: 'FETCH_END', data: {} });

            return;
        }

        dispatch({ type: 'FETCH_START' });
        findLocationsByParentLocationId(
            {
                ...restInfo,
                parentLocationId: locationData.parentLocationId,
                sortClause,
                sortOrder,
                limit,
                offset,
                gridView,
            },
            (response) => {
                if (effectCleaned) {
                    return;
                }

                dispatch({ type: 'FETCH_END', data: response });
            }
        );

        return () => {
            effectCleaned = true;
        };
    }, [
        restInfo,
        sortClause,
        sortOrder,
        locationData.parentLocationId,
        locationData.subitems.length,
        limit,
        offset,
        gridView,
        locationData.collapsed,
    ]);

    return [state.data, !state.dataFetched];
};
