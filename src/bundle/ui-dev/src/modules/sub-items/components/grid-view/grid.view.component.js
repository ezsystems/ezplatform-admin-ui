import React from 'react';
import PropTypes from 'prop-types';

import GridViewItemComponent from './grid.view.item.component';

const GridViewComponent = ({ items, generateLink }) => (
    <div className="ibexa-grid-view">
        {items.map((item) => (
            <GridViewItemComponent key={item.id} item={item} generateLink={generateLink} />
        ))}
    </div>
);

GridViewComponent.propTypes = {
    items: PropTypes.arrayOf(PropTypes.object),
    generateLink: PropTypes.func.isRequired,
};

export default GridViewComponent;
