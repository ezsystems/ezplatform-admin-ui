import React, { useContext, useMemo } from 'react';
import PropTypes from 'prop-types';

import InputSearch from '../input-search/input.search';
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
    const backTitle = Translator.trans(/*@Desc("Back")*/ 'back.label', {}, 'universal_discovery_widget');

    return (
        <div className="c-top-menu">
            <h2
                className="c-top-menu__title-wrapper"
                data-tooltip-container-selector=".c-udw-tab"
                title={title}
            >
                {title}
            </h2>
            <div className="c-top-menu__actions-wrapper">
                {sortedActions.map((action) => {
                    const Component = action.component;

                    return <Component key={action.id} isDisabled={actionsDisabledMap[action.id]} />;
                })}
            </div>
            <InputSearch />
            <span className="c-top-menu__cancel-btn-wrapper">
                <button
                    className="c-top-menu__cancel-btn btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text"
                    type="button"
                    onClick={cancelUDW}
                    title={backTitle}
                    data-tooltip-container-selector=".c-top-menu__cancel-btn-wrapper">
                    <Icon name="discard" extraClasses="ibexa-icon--medium" />
                </button>
            </span>
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
