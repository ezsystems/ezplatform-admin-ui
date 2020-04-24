import React, { useContext, useEffect } from 'react';
import PropTypes from 'prop-types';

import {
    UDWContext,
    SelectedLocationsContext,
    RestInfoContext,
    MultipleConfigContext,
    ContainersOnlyContext,
    AllowedContentTypesContext,
} from '../../universal.discovery.module';
import { findLocationsById } from '../../services/universal.discovery.service';
import PureToggleSelectionButton from '../pure-toggle-selection-button/pure.toggle.selection.button';

const TreeItemToggleSelectionButton = ({ locationId, isContainer, contentTypeIdentifier }) => {
    const isUDW = useContext(UDWContext);

    useEffect(() => {
        window.eZ.helpers.tooltips.parse(window.document.querySelector('.c-list'));
    }, []);

    if (!isUDW) {
        return null;
    }

    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const [multiple, multipleItemsLimit] = useContext(MultipleConfigContext);
    const containersOnly = useContext(ContainersOnlyContext);
    const allowedContentTypes = useContext(AllowedContentTypesContext);
    const restInfo = useContext(RestInfoContext);
    const isNotSelectable =
        (containersOnly && !isContainer) || (allowedContentTypes && !allowedContentTypes.includes(contentTypeIdentifier));
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

    if (!multiple || isNotSelectable) {
        return null;
    }

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
    isContainer: PropTypes.bool.isRequired,
    contentTypeIdentifier: PropTypes.string.isRequired,
};

export default TreeItemToggleSelectionButton;
