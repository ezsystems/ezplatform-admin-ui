import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import { SelectedLocationsContext } from '../../universal.discovery.module';

const ToggleSelectionCheckbox = ({ location, isDisabled }) => {
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const isSelected = selectedLocations.some((selectedItem) => selectedItem.location.id === location.id);
    const toggleSelection = () => {
        const action = isSelected ? { type: 'REMOVE_SELECTED_LOCATION', id: location.id } : { type: 'ADD_SELECTED_LOCATION', location };

        dispatchSelectedLocationsAction(action);
    };

    return (
        <input
            type="checkbox"
            className="ibexa-input ibexa-input--checkbox"
            checked={isSelected}
            disabled={isDisabled}
            onChange={toggleSelection}
        />
    );
};

ToggleSelectionCheckbox.propTypes = {
    location: PropTypes.object.isRequired,
    isDisabled: PropTypes.bool,
};

ToggleSelectionCheckbox.defaultProps = {
    isDisabled: false,
}

export default ToggleSelectionCheckbox;
