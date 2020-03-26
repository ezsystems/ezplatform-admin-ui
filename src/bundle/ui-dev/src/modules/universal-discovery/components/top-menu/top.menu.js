import React, { useContext, useMemo } from 'react';
import PropTypes from 'prop-types';

import Icon from '../../../common/icon/icon';

import { TitleContext, CancelContext } from '../../universal.discovery.module';

const TopMenu = ({ actionsDisabledMap }) => {
    const title = useContext(TitleContext);
    const cancelUDW = useContext(CancelContext);
    const sortedActions = useMemo(() => {
        const actions = [...window.eZ.adminUiConfig.universalDiscoveryWidget.topMenuActions];

        return actions.sort((actionA, actionB) => {
            return actionB.priority - actionA.priority;
        });
    }, []);

    return (
        <div className="c-top-menu">
            <span className="c-top-menu__cancel-btn-wrapper">
                <button className="c-top-menu__cancel-btn" type="button" onClick={cancelUDW}>
                    <Icon name="caret-back" />
                </button>
            </span>
            <span className="c-top-menu__title-wrapper">{title}</span>
            <div className="c-top-menu__actions-wrapper">
                {sortedActions.map((action) => {
                    const Component = action.component;

                    return <Component key={action.id} isDisabled={actionsDisabledMap[action.id]} />;
                })}
            </div>
        </div>
    );
};

TopMenu.propTypes = {
    actionsDisabledMap: PropTypes.object,
};

TopMenu.defaultProps = {
    actionsDisabledMap: {
        'content-create-button': false,
        'sort-switcher': false,
        'view-switcher': false,
    },
};

export default TopMenu;
