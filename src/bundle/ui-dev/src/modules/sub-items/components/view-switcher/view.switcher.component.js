import React from 'react';
import PropTypes from 'prop-types';
import Icon from '../../../common/icon/icon';

import { VIEW_MODE_TABLE, VIEW_MODE_GRID } from '../../sub.items.module';

const ViewSwitcherComponent = ({ onViewChange, activeView, isDisabled }) => {
    let componentClassName = 'c-view-switcher';

    if (isDisabled) {
        componentClassName = `${componentClassName} ${componentClassName}--disabled`;
    }

    const viewBtnLabel = Translator.trans(/*@Desc("View")*/ 'switch_to_list_view.btn.label', {}, 'sub_items');
    const listViewBtnTitle = Translator.trans(/*@Desc("View as list")*/ 'switch_to_list_view.btn.title', {}, 'sub_items');
    const gridViewBtnTitle = Translator.trans(/*@Desc("View as grid")*/ 'switch_to_grid_view.btn.title', {}, 'sub_items');
    const isTableViewActive = activeView === VIEW_MODE_TABLE;
    const viewBtnTitle = isTableViewActive ? gridViewBtnTitle : listViewBtnTitle;
    const viewBtnIconName = isTableViewActive ? 'view-grid' : 'view-list';
    const switchView = () => {
        const newView = isTableViewActive ? VIEW_MODE_GRID : VIEW_MODE_TABLE;

        onViewChange(newView);
    };
    const btnClassName = 'btn ibexa-btn ibexa-btn--ghost ibexa-btn--icon-right';

    return (
        <div className={componentClassName}>
            <button type="button" className={btnClassName} title={viewBtnTitle} onClick={switchView} disabled={isDisabled}>
                {viewBtnLabel} <Icon name={viewBtnIconName} extraClasses="ibexa-icon--small" />
            </button>
        </div>
    );
};

ViewSwitcherComponent.propTypes = {
    onViewChange: PropTypes.func.isRequired,
    activeView: PropTypes.string.isRequired,
    isDisabled: PropTypes.bool.isRequired,
};

export default ViewSwitcherComponent;
