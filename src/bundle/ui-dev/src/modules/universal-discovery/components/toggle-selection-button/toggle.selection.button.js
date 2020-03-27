import React, { useContext, useEffect, useRef, useState } from 'react';
import PropTypes from 'prop-types';

import Icon from '../../../common/icon/icon';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import { SelectedLocationsContext, MultipleConfigContext, ContainersOnlyContext } from '../../universal.discovery.module';

const ToggleSelectionButton = ({ location }) => {
    const refToggleSelectionButton = useRef(null);
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const [multiple, multipleItemsLimit] = useContext(MultipleConfigContext);
    const isSelected = selectedLocations.some((selectedItem) => selectedItem.location.id === location.id);
    const addLabel = Translator.trans(/*@Desc("Add")*/ 'browser.add', {}, 'universal_discovery_widget');
    const selectedLabel = Translator.trans(/*@Desc("Selected")*/ 'browser.selected', {}, 'universal_discovery_widget');
    const toggleSelectionLabel = isSelected ? selectedLabel : addLabel;
    const iconName = isSelected ? 'checkmark' : 'create';
    const className = createCssClassNames({
        'c-toggle-selection-button': true,
        'c-toggle-selection-button--selected': isSelected,
    });
    const toggleSelection = () => {
        const action = isSelected ? { type: 'REMOVE_SELECTED_LOCATION', id: location.id } : { type: 'ADD_SELECTED_LOCATION', location };

        dispatchSelectedLocationsAction(action);
    };

    useEffect(() => {
        window.eZ.helpers.tooltips.hideAll(window.document.querySelector('.c-udw-tab'));

        // Title on toggler selection button is dynamic, for this we have to change 'data-original-title'.
        // Remove title is neccessary to prevent situation when we have bootsrap and native title.
        if (refToggleSelectionButton.current.getAttribute('data-original-title')) {
            refToggleSelectionButton.current.removeAttribute('title');
        }

        refToggleSelectionButton.current.setAttribute('data-original-title', toggleSelectionLabel);
    }, [isSelected]);

    if (multiple && !isSelected && selectedLocations.length >= multipleItemsLimit && multipleItemsLimit !== 0) {
        return null;
    }

    return (
        <button
            ref={refToggleSelectionButton}
            className={className}
            onClick={toggleSelection}
            title={toggleSelectionLabel}
            data-tooltip-container-selector=".c-udw-tab">
            <Icon name={iconName} extraClasses="ez-icon--small" />
        </button>
    );
};

ToggleSelectionButton.propTypes = {
    location: PropTypes.object.isRequired,
};

export default ToggleSelectionButton;
