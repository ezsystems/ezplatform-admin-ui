import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import Icon from '../../../common/icon/icon';
import MenuButton from '../menu-button/menu.button';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import { CurrentViewContext, VIEWS } from '../../universal.discovery.module';

const ViewSwitcher = ({ isDisabled }) => {
    const [currentView, setCurrentView] = useContext(CurrentViewContext);
    const className = createCssClassNames({
        'c-udw-view-switcher': true,
        'c-udw-view-switcher--disabled': isDisabled,
    });

    return (
        <div className={className}>
            {VIEWS.map((view) => {
                const extraClasses = view.id === currentView ? 'c-menu-button--selected' : '';
                const onClick = () => {
                    setCurrentView(view.id);
                    window.eZ.helpers.tooltips.hideAll();
                };

                return (
                    <MenuButton
                        key={view.id}
                        extraClasses={extraClasses}
                        onClick={onClick}
                        isDisabled={isDisabled}
                        title={view.tooltipLabel}>
                        <Icon name={view.icon} extraClasses="ez-icon--small-medium" />
                    </MenuButton>
                );
            })}
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
