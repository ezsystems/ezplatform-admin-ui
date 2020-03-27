import React from 'react';
import PropTypes from 'prop-types';

import ViewSwitcherButton from './view.switcher.button.component';

const ViewSwitcherComponent = ({ onViewChange, activeView, isDisabled }) => {
    let componentClassName = 'c-view-switcher';

    if (isDisabled) {
        componentClassName = `${componentClassName} ${componentClassName}--disabled`;
    }

    const listViewBtnLabel = Translator.trans(/*@Desc("View as list")*/ 'switch_to_list_view.btn.label', {}, 'sub_items');
    const gridViewBtnLabel = Translator.trans(/*@Desc("View as grid")*/ 'switch_to_grid_view.btn.label', {}, 'sub_items');

    return (
        <div className={componentClassName}>
            <ViewSwitcherButton
                id="table"
                icon="view-list"
                title={listViewBtnLabel}
                onClick={onViewChange}
                activeView={activeView}
                isDisabled={isDisabled}
            />
            <ViewSwitcherButton
                id="grid"
                icon="view-grid"
                title={gridViewBtnLabel}
                onClick={onViewChange}
                activeView={activeView}
                isDisabled={isDisabled}
            />
        </div>
    );
};

ViewSwitcherComponent.propTypes = {
    onViewChange: PropTypes.func.isRequired,
    activeView: PropTypes.string.isRequired,
    isDisabled: PropTypes.bool.isRequired,
};

export default ViewSwitcherComponent;
