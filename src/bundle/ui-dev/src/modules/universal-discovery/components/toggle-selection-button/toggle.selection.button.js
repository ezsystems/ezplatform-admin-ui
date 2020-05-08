import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import { SelectedLocationsContext } from '../../universal.discovery.module';
import PureToggleSelectionButton from '../pure-toggle-selection-button/pure.toggle.selection.button';

const ToggleSelectionButton = ({ location }) => {
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const isSelected = selectedLocations.some((selectedItem) => selectedItem.location.id === location.id);
    const toggleSelection = () => {
        const action = isSelected ? { type: 'REMOVE_SELECTED_LOCATION', id: location.id } : { type: 'ADD_SELECTED_LOCATION', location };

        dispatchSelectedLocationsAction(action);
    };

    return <PureToggleSelectionButton isSelected={isSelected} toggleSelection={toggleSelection} />;
};

ToggleSelectionButton.propTypes = {
    location: PropTypes.object.isRequired,
};

export default ToggleSelectionButton;
