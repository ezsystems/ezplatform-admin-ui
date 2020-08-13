import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import Icon from '../../../common/icon/icon';

import {
    CreateContentWidgetContext,
    MarkedLocationIdContext,
    LoadedLocationsMapContext,
    ContentOnTheFlyConfigContext,
    SelectedLocationsContext,
    MultipleConfigContext,
} from '../../universal.discovery.module';

const ContentCreateButton = ({ isDisabled }) => {
    const [markedLocationId, setMarkedLocationId] = useContext(MarkedLocationIdContext);
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useContext(LoadedLocationsMapContext);
    const [createContentVisible, setCreateContentVisible] = useContext(CreateContentWidgetContext);
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const [multiple, multipleItemsLimit] = useContext(MultipleConfigContext);
    const { hidden, allowedLocations } = useContext(ContentOnTheFlyConfigContext);
    const createLabel = Translator.trans(/*@Desc("Create")*/ 'create_content.create', {}, 'universal_discovery_widget');
    const toggleContentCreateVisibility = () => {
        window.eZ.helpers.tooltips.hideAll();
        setCreateContentVisible((prevState) => !prevState);
    };
    let selectedLocation = loadedLocationsMap.find((loadedLocation) => loadedLocation.parentLocationId === markedLocationId);

    if (!selectedLocation && loadedLocationsMap.length) {
        selectedLocation = loadedLocationsMap[loadedLocationsMap.length - 1].subitems.find(
            (subitem) => subitem.location.id === markedLocationId
        );
    }

    const isAllowedLocation = selectedLocation && (!allowedLocations || allowedLocations.includes(selectedLocation.parentLocationId));
    const hasAccess =
        !selectedLocation ||
        !selectedLocation.permissions ||
        (selectedLocation.permissions && selectedLocation.permissions.create.hasAccess);
    const isLimitReached = multiple && multipleItemsLimit !== 0 && selectedLocations.length >= multipleItemsLimit;

    if (hidden) {
        return null;
    }

    return (
        <div className="c-content-create-button">
            <button
                className="c-content-create-button__btn btn btn-link"
                disabled={isDisabled || !hasAccess || !isAllowedLocation || isLimitReached}
                onClick={toggleContentCreateVisibility}
                data-tooltip-container-selector=".c-top-menu"
                title={createLabel}>
                <Icon name="create" extraClasses="ez-icon--small-medium ez-icon--primary" /> {createLabel}
            </button>
        </div>
    );
};

ContentCreateButton.propTypes = {
    isDisabled: PropTypes.bool,
};

ContentCreateButton.defaultProps = {
    isDisabled: false,
};

eZ.addConfig(
    'adminUiConfig.universalDiscoveryWidget.topMenuActions',
    [
        {
            id: 'content-create-button',
            priority: 30,
            component: ContentCreateButton,
        },
    ],
    true
);

export default ContentCreateButton;
