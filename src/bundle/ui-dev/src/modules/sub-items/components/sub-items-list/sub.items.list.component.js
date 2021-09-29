import React from 'react';
import PropTypes from 'prop-types';
import TableViewComponent from '../table-view/table.view.component.js';
import GridViewComponent from '../grid-view/grid.view.component.js';
import { VIEW_MODE_GRID, VIEW_MODE_TABLE } from '../../sub.items.module.js';

const SubItemsListComponent = (props) => {
    const views = {
        [VIEW_MODE_TABLE]: TableViewComponent,
        [VIEW_MODE_GRID]: GridViewComponent,
    };
    const Component = views[props.activeView];

    return <Component {...props} />;
};

SubItemsListComponent.propTypes = {
    activeView: PropTypes.string.isRequired,
    items: PropTypes.arrayOf(PropTypes.object),
    handleItemPriorityUpdate: PropTypes.func.isRequired,
    handleEditItem: PropTypes.func.isRequired,
    generateLink: PropTypes.func.isRequired,
    languages: PropTypes.object.isRequired,
    onItemSelect: PropTypes.func.isRequired,
    toggleAllItemsSelect: PropTypes.func.isRequired,
    selectedLocationsIds: PropTypes.instanceOf(Set).isRequired,
    onSortChange: PropTypes.func.isRequired,
    sortClause: PropTypes.string.isRequired,
    sortOrder: PropTypes.string.isRequired,
    languageContainerSelector: PropTypes.string.isRequired,
};

export default SubItemsListComponent;
