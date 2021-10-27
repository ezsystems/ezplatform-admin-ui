import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import { SelectedLocationsContext } from '../../universal.discovery.module';

const ToggleSelection = ({ multiple, location, isHidden }) => {
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const isSelected = selectedLocations.some((selectedItem) => selectedItem.location.id === location.id);
    const inputType = multiple ? 'checkbox' : 'radio';
    const className = createCssClassNames({
        'c-udw-toggle-selection ibexa-input': true,
        'ibexa-input--checkbox': multiple,
        'ibexa-input--radio': !multiple,
        'c-udw-toggle-selection--hidden': isHidden,
    });
    const toggleSelection = () => {
        if (!multiple) {
            dispatchSelectedLocationsAction({ type: 'CLEAR_SELECTED_LOCATIONS' });
            dispatchSelectedLocationsAction({ type: 'ADD_SELECTED_LOCATION', location });
        } else {
            const action = isSelected
                ? { type: 'REMOVE_SELECTED_LOCATION', id: location.id }
                : { type: 'ADD_SELECTED_LOCATION', location };

            dispatchSelectedLocationsAction(action);
        }
    };

    return (
        <input
            type={inputType}
            className={className}
            checked={isSelected}
            disabled={isHidden}
            onChange={toggleSelection}
        />
    );
};

ToggleSelection.propTypes = {
    location: PropTypes.object.isRequired,
    multiple: PropTypes.bool.isRequired,
    isHidden: PropTypes.bool,
};

ToggleSelection.defaultProps = {
    isHidden: false,
}

export default ToggleSelection;
