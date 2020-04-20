import React, { useContext, useEffect } from 'react';
import PropTypes from 'prop-types';

import { SelectedLocationsContext, RestInfoContext } from '../../universal.discovery.module';
import { findLocationsById } from '../../services/universal.discovery.service';
import PureToggleSelectionButton from '../pure-toggle-selection-button/pure.toggle.selection.button';

const TreeItemToggleSelectionButton = ({ locationId }) => {
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const restInfo = useContext(RestInfoContext);
    const isSelected = selectedLocations.some((selectedItem) => selectedItem.location.id === locationId);
    const toggleSelection = () => {
        if (isSelected) {
            dispatchSelectedLocationsAction({ type: 'REMOVE_SELECTED_LOCATION', id: locationId });
        } else {
            findLocationsById({ ...restInfo, id: locationId }, ([location]) => {
                dispatchSelectedLocationsAction({ type: 'ADD_SELECTED_LOCATION', location });
            });
        }
    };

    useEffect(() => {
        window.eZ.helpers.tooltips.parse(window.document.querySelector('.c-list'));
    }, []);

    return <PureToggleSelectionButton isSelected={isSelected} toggleSelection={toggleSelection} />;
};

eZ.addConfig(
    'adminUiConfig.contentTreeWidget.itemActions',
    [
        {
            id: 'toggle-selection-button',
            priority: 30,
            component: TreeItemToggleSelectionButton,
        },
    ],
    true
);

TreeItemToggleSelectionButton.propTypes = {
    locationId: PropTypes.number.isRequired,
};

export default TreeItemToggleSelectionButton;
