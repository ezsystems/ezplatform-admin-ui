import React, { useEffect } from 'react';
import PropTypes from 'prop-types';

import ToggleSelectionButton from '../toggle-selection-button/toggle.selection.button';

const SelectedItemEditButton = ({ location }) => {
    useEffect(() => {
        window.eZ.helpers.tooltips.parse(window.document.querySelector('.c-list'));
    }, []);

    return <ToggleSelectionButton location={location} />;
};

eZ.addConfig(
    'adminUiConfig.contentTreeWidget.itemActions',
    [
        {
            id: 'add-button',
            priority: 30,
            component: SelectedItemEditButton,
        },
    ],
    true
);

SelectedItemEditButton.propTypes = {
    location: PropTypes.object.isRequired,
};

export default SelectedItemEditButton;
