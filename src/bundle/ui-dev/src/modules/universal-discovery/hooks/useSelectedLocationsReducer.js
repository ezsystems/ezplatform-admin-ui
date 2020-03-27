import { useReducer } from 'react';

const initialState = [];

const selectedLocationsReducer = (state, action) => {
    switch (action.type) {
        case 'ADD_SELECTED_LOCATION':
            return [...state, { location: action.location, permissions: action.permissions }];
        case 'REMOVE_SELECTED_LOCATION':
            return state.filter((selectedItem) => selectedItem.location.id !== action.id);
        case 'CLEAR_SELECTED_LOCATIONS':
            return [];
        case 'REPLACE_SELECTED_LOCATIONS':
            return action.locations;
        default:
            throw new Error();
    }
};

export const useSelectedLocationsReducer = (state = initialState) => {
    const [selectedLocations, dispatchSelectedLocationsAction] = useReducer(selectedLocationsReducer, state);

    return [selectedLocations, dispatchSelectedLocationsAction];
};
