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
import ToggleSelection from '../toggle-selection/toggle.selection';

const TreeItemToggleSelection = ({ locationId, isContainer, contentTypeIdentifier }) => {
    const isUDW = useContext(UDWContext);

    useEffect(() => {
        window.eZ.helpers.tooltips.parse(window.document.querySelector('.c-list'));
    }, []);

    if (!isUDW) {
        return null;
    }

    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const [multiple] = useContext(MultipleConfigContext);
    const containersOnly = useContext(ContainersOnlyContext);
    const allowedContentTypes = useContext(AllowedContentTypesContext);
    const restInfo = useContext(RestInfoContext);
    const isNotSelectable =
        (containersOnly && !isContainer) || (allowedContentTypes && !allowedContentTypes.includes(contentTypeIdentifier));
    const location = {
        id: locationId,
    };
    const dispatchSelectedLocationsActionWrapper = (action) => {
        if (action.location !== undefined) {
            findLocationsById({ ...restInfo, id: action.location.id }, ([selectedLocation]) => {
                dispatchSelectedLocationsAction({ ...action, location: selectedLocation });
            });
        } else {
            dispatchSelectedLocationsAction(action);
        }
    };

    return (
        <SelectedLocationsContext.Provider
            value={[
                selectedLocations,
                dispatchSelectedLocationsActionWrapper,
            ]}
        >
            <ToggleSelection location={location} multiple={multiple} isHidden={isNotSelectable} />
            {isNotSelectable && <div class="c-list-item__prefix-actions-item-empty"></div>}
        </SelectedLocationsContext.Provider>
    );
};

eZ.addConfig(
    'adminUiConfig.contentTreeWidget.prefixActions',
    [
        {
            id: 'toggle-selection-button',
            priority: 30,
            component: TreeItemToggleSelection,
        },
    ],
    true
);

TreeItemToggleSelection.propTypes = {
    locationId: PropTypes.number.isRequired,
    isContainer: PropTypes.bool.isRequired,
    contentTypeIdentifier: PropTypes.string.isRequired,
};

export default TreeItemToggleSelection;
