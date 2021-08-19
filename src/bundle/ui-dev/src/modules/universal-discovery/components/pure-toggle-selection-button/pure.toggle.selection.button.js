import React, { useContext, useEffect, useRef } from 'react';
import PropTypes from 'prop-types';

import Icon from '../../../common/icon/icon';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import { SelectedLocationsContext, MultipleConfigContext } from '../../universal.discovery.module';

const PureToggleSelectionButton = ({ isSelected, toggleSelection }) => {
    const refPureToggleSelectionButton = useRef(null);
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const [multiple, multipleItemsLimit] = useContext(MultipleConfigContext);
    const addLabel = Translator.trans(/*@Desc("Add")*/ 'browser.add', {}, 'universal_discovery_widget');
    const selectedLabel = Translator.trans(/*@Desc("Selected")*/ 'browser.selected', {}, 'universal_discovery_widget');
    const toggleSelectionLabel = isSelected ? selectedLabel : addLabel;
    const iconName = isSelected ? 'checkmark' : 'create';
    const className = createCssClassNames({
        'c-toggle-selection-button': true,
        'c-toggle-selection-button--selected': isSelected,
    });

    useEffect(() => {
        window.eZ.helpers.tooltips.hideAll(window.document.querySelector('.c-udw-tab'));

        if (!refPureToggleSelectionButton.current) {
            return;
        }

        // Title on toggler selection button is dynamic, for this we have to change 'data-bs-original-title'.
        // Remove title is neccessary to prevent situation when we have bootsrap and native title.
        if (refPureToggleSelectionButton.current.getAttribute('data-bs-original-title')) {
            refPureToggleSelectionButton.current.removeAttribute('title');
        }

        refPureToggleSelectionButton.current.setAttribute('data-bs-original-title', toggleSelectionLabel);
    }, [isSelected]);

    if (multiple && !isSelected && selectedLocations.length >= multipleItemsLimit && multipleItemsLimit !== 0) {
        return null;
    }

    return (
        <button
            ref={refPureToggleSelectionButton}
            className={className}
            onClick={toggleSelection}
            title={toggleSelectionLabel}
            data-tooltip-container-selector=".c-udw-tab">
            <Icon name={iconName} extraClasses="ibexa-icon--small" />
        </button>
    );
};

PureToggleSelectionButton.propTypes = {
    isSelected: PropTypes.object.isRequired,
    toggleSelection: PropTypes.func.isRequired,
};

export default PureToggleSelectionButton;
