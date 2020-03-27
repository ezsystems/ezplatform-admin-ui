import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import MenuButton from '../menu-button/menu.button';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import { SortingContext, SortOrderContext, SORTING_OPTIONS } from '../../universal.discovery.module';

const ASCENDING_ORDER = 'ascending';
const DESCENDING_ORDER = 'descending';

const SortSwitcher = ({ isDisabled }) => {
    const [sorting, setSorting] = useContext(SortingContext);
    const [sortOrder, setSortOrder] = useContext(SortOrderContext);
    const className = createCssClassNames({
        'c-sort-switcher': true,
        'c-sort-switcher--disabled': isDisabled,
    });

    return (
        <div className={className}>
            {SORTING_OPTIONS.map((option) => {
                const extraClasses = createCssClassNames({
                    'c-menu-button--selected': option.sortClause === sorting,
                    'c-menu-button--sorted-asc': sortOrder === ASCENDING_ORDER,
                    'c-menu-button--sorted-desc': sortOrder === DESCENDING_ORDER,
                });
                const onClick = () => {
                    setSorting(option.sortClause);

                    if (sorting === option.sortClause) {
                        setSortOrder(sortOrder === ASCENDING_ORDER ? DESCENDING_ORDER : ASCENDING_ORDER);
                    }
                };

                return (
                    <MenuButton
                        key={option.sortClause}
                        extraClasses={extraClasses}
                        onClick={onClick}
                        isDisabled={isDisabled}
                        title={option.tooltipLabel}>
                        {option.label}
                    </MenuButton>
                );
            })}
        </div>
    );
};

SortSwitcher.propTypes = {
    isDisabled: PropTypes.bool,
};

SortSwitcher.defaultProps = {
    isDisabled: false,
};

eZ.addConfig(
    'adminUiConfig.universalDiscoveryWidget.topMenuActions',
    [
        {
            id: 'sort-switcher',
            priority: 20,
            component: SortSwitcher,
        },
    ],
    true
);

export default SortSwitcher;
