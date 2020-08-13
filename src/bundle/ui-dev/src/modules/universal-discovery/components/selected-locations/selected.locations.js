import React, { useContext, useState, useEffect, useRef, Fragment } from 'react';

import Icon from '../../../common/icon/icon';
import SelectedLocationsItem from './selected.locations.item';
import { createCssClassNames } from '../../../common/helpers/css.class.names';

import { SelectedLocationsContext, ConfirmContext, AllowConfirmationContext } from '../../universal.discovery.module';

const SelectedLocations = () => {
    const refSelectedLocations = useRef(null);
    const refTogglerButton = useRef(null);
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const allowConfirmation = useContext(AllowConfirmationContext);
    const onConfirm = useContext(ConfirmContext);
    const [isExpanded, setIsExpanded] = useState(false);
    const className = createCssClassNames({
        'c-selected-locations': true,
        'c-selected-locations--expanded': isExpanded,
    });
    const expandLabel = Translator.trans(/*@Desc("Expand sidebar")*/ 'selected_locations.expand.sidebar', {}, 'universal_discovery_widget');
    const collapseLabel = Translator.trans(
        /*@Desc("Collapse sidebar")*/ 'selected_locations.collapse.sidebar',
        {},
        'universal_discovery_widget'
    );
    const togglerLabel = isExpanded ? collapseLabel : expandLabel;
    const clearSelection = () => {
        window.eZ.helpers.tooltips.hideAll(refSelectedLocations.current);
        dispatchSelectedLocationsAction({ type: 'CLEAR_SELECTED_LOCATIONS' });
    };
    const toggleExpanded = () => {
        setIsExpanded(!isExpanded);
    };
    const renderSelectionCounter = () => {
        const selectedLabel = Translator.trans(/*@Desc("selected")*/ 'selected_locations.selected', {}, 'universal_discovery_widget');

        return (
            <div className="c-selected-locations__selection-counter">
                <span className="c-selected-locations__selection-count">{selectedLocations.length}</span>
                <span className="c-selected-locations__selection-count-label">{selectedLabel}</span>
            </div>
        );
    };
    const renderToggleButton = () => {
        const iconName = isExpanded ? 'caret-next' : 'caret-back';

        return (
            <button
                ref={refTogglerButton}
                type="button"
                className="c-selected-locations__toggle-button"
                onClick={toggleExpanded}
                title={togglerLabel}
                data-tooltip-container-selector=".c-udw-tab">
                <Icon name={iconName} extraClasses="ez-icon--medium" />
            </button>
        );
    };
    const renderActionButtons = () => {
        const confirmSelectionLabel = Translator.trans(
            /*@Desc("Confirm selection")*/ 'selected_locations.confirm_selection',
            {},
            'universal_discovery_widget'
        );
        const clearAllLabel = Translator.trans(/*@Desc("Clear all")*/ 'selected_locations.clear_all', {}, 'universal_discovery_widget');

        return (
            <Fragment>
                <button
                    type="button"
                    className="c-selected-locations__confirm-button"
                    onClick={() => onConfirm()}
                    title={confirmSelectionLabel}
                    data-tooltip-container-selector=".c-udw-tab">
                    <Icon name="checkmark" extraClasses="ez-icon--small-medium ez-icon--base-dark" />
                </button>
                <button
                    type="button"
                    className="c-selected-locations__clear-selection-button"
                    onClick={clearSelection}
                    title={clearAllLabel}
                    data-tooltip-container-selector=".c-udw-tab">
                    <Icon name="trash" extraClasses="ez-icon--small-medium ez-icon--dark" />
                </button>
            </Fragment>
        );
    };
    const renderLocationsList = () => {
        if (!isExpanded) {
            return null;
        }

        return (
            <div className="c-selected-locations__items-wrapper">
                {selectedLocations.map((selectedLocation) => (
                    <SelectedLocationsItem
                        key={selectedLocation.location.id}
                        location={selectedLocation.location}
                        permissions={selectedLocation.permissions}
                    />
                ))}
            </div>
        );
    };

    useEffect(() => {
        window.eZ.helpers.tooltips.parse(refSelectedLocations.current);
        window.eZ.helpers.tooltips.hideAll();

        if (refTogglerButton.current) {
            refTogglerButton.current.dataset.originalTitle = togglerLabel;
        }
    }, [isExpanded]);

    if (!allowConfirmation) {
        return null;
    }

    return (
        <div className={className} ref={refSelectedLocations}>
            <div className="c-selected-locations__header">
                <div className="c-selected-locations__actions-wrapper">
                    {renderToggleButton()}
                    {!isExpanded && renderActionButtons()}
                    {isExpanded && renderSelectionCounter()}
                    {isExpanded && renderActionButtons()}
                </div>
                {!isExpanded && renderSelectionCounter()}
            </div>
            {renderLocationsList()}
        </div>
    );
};

export default SelectedLocations;
