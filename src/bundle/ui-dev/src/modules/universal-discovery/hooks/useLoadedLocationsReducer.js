import { useReducer } from 'react';

const initialState = [{ parentLocationId: 1, subitems: [] }];
const cutLocationsAfter = (state, action) => {
    const itemIndex = state.findIndex((data) => data.parentLocationId === action.locationId);

    if (itemIndex === -1) {
        return state;
    }

    return state.slice(0, itemIndex + 1);
};
const updateLocationsMap = (state, action) => {
    const parentLocationIndex = state.findIndex((location) => location.parentLocationId === action.data.parentLocationId);
    let updatedState = [...state];

    if (parentLocationIndex !== -1) {
        updatedState[parentLocationIndex] = action.data;

        return updatedState;
    }

    const childrenIndex = state.findIndex((data) => {
        return data.subitems.find((item) => item.location.id === action.data.parentLocationId);
    });

    if (childrenIndex !== -1) {
        updatedState = updatedState.slice(0, childrenIndex + 1);
    }

    updatedState.push(action.data);

    return updatedState;
};
const setLocations = (state, action) => {
    return action.data;
};
const clearLoactions = () => {
    return [];
};

const loadedLocationsReducer = (state, action) => {
    switch (action.type) {
        case 'CUT_LOCATIONS':
            return cutLocationsAfter(state, action);
        case 'UPDATE_LOCATIONS':
            return updateLocationsMap(state, action);
        case 'SET_LOCATIONS':
            return setLocations(state, action);
        case 'CLEAR_LOCATIONS':
            return clearLoactions();
        default:
            throw new Error();
    }
};

export const useLoadedLocationsReducer = (state = initialState) => {
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useReducer(loadedLocationsReducer, state);

    return [loadedLocationsMap, dispatchLoadedLocationsAction];
};
