import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import SimpleDropdown from '../simple-dropdown/simple.dropdown';

import { CurrentViewContext, VIEWS } from '../../universal.discovery.module';

const ViewSwitcher = ({ isDisabled }) => {
    const [currentView, setCurrentView] = useContext(CurrentViewContext);
    const onOptionClick = (view) => {
        setCurrentView(view.id);
    }
    const selectedOption = VIEWS.find((view) => view.id === currentView);

    return (
        <div className="c-udw-view-switcher">
            <SimpleDropdown
                options={VIEWS}
                selectedOption={selectedOption}
                onOptionClick={onOptionClick}
                isDisabled={isDisabled}
            />
        </div>
    );
};

ViewSwitcher.propTypes = {
    isDisabled: PropTypes.bool,
};

ViewSwitcher.defaultProps = {
    isDisabled: false,
};

eZ.addConfig(
    'adminUiConfig.universalDiscoveryWidget.topMenuActions',
    [
        {
            id: 'view-switcher',
            priority: 10,
            component: ViewSwitcher,
        },
    ],
    true
);

export default ViewSwitcher;
