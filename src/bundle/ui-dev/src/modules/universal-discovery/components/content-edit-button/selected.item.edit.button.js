import React from 'react';
import PropTypes from 'prop-types';

import ContentEditButton from '../content-edit-button/content.edit.button';

const SelectedItemEditButton = ({ location, permissions }) => {
    const hasAccess = permissions && permissions.edit.hasAccess;

    return (
        <div className="c-selected-item-edit-button">
            <ContentEditButton version={location.ContentInfo.Content.CurrentVersion.Version} location={location} isDisabled={!hasAccess} />
        </div>
    );
};

eZ.addConfig(
    'adminUiConfig.universalDiscoveryWidget.selectedItemActions',
    [
        {
            id: 'content-edit-button',
            priority: 30,
            component: SelectedItemEditButton,
        },
    ],
    true
);

SelectedItemEditButton.propTypes = {
    location: PropTypes.object.isRequired,
    permissions: PropTypes.object.isRequired,
};

export default SelectedItemEditButton;
