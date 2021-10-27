import React, { useEffect, useRef } from 'react';
import PropTypes from 'prop-types';

import ContentTableItem from './content.table.item';

import Pagination from '../../../common/pagination/pagination';

const ContentTable = ({ count, itemsPerPage, items, activePageIndex, title, onPageChange }) => {
    const refContentTable = useRef(null);
    const nameLabel = Translator.trans(/*@Desc("Name")*/ 'content_table.name', {}, 'universal_discovery_widget');
    const modifiedLabel = Translator.trans(/*@Desc("Modified")*/ 'content_table.modified', {}, 'universal_discovery_widget');
    const contentTypeLabel = Translator.trans(/*@Desc("Content Type")*/ 'content_table.content_type', {}, 'universal_discovery_widget');
    const renderHeaderCell = (label) => (
        <th class="ibexa-table__header-cell">
            <span class="ibexa-table__header-cell-text-wrapper">{label}</span>
        </th>
    )

    useEffect(() => {
        window.eZ.helpers.tooltips.parse(refContentTable.current);
    }, []);

    return (
        <div className="c-content-table" ref={refContentTable}>
            <div className="ibexa-table-header">
                <div class="ibexa-table-header__headline">
                    {title}
                </div>
            </div>
            <div className="ibexa-scrollable-wrapper">
                <table className="ibexa-table table">
                    <thead>
                        <tr class="ibexa-table__head-row">
                            {renderHeaderCell()}
                            {renderHeaderCell()}
                            {renderHeaderCell(nameLabel)}
                            {renderHeaderCell(modifiedLabel)}
                            {renderHeaderCell(contentTypeLabel)}
                        </tr>
                    </thead>
                    <tbody class="ibexa-table__body">
                        {items.map((item) => (
                            <ContentTableItem key={item.id} location={item} />
                        ))}
                    </tbody>
                </table>
            </div>
            <div className="c-content-table__pagination">
                <Pagination
                    proximity={1}
                    itemsPerPage={itemsPerPage}
                    activePageIndex={activePageIndex}
                    totalCount={count}
                    onPageChange={onPageChange}
                    disabled={false}
                />
            </div>
        </div>
    );
};

ContentTable.propTypes = {
    count: PropTypes.number.isRequired,
    itemsPerPage: PropTypes.number.isRequired,
    activePageIndex: PropTypes.number.isRequired,
    items: PropTypes.array.isRequired,
    title: PropTypes.string.isRequired,
    onPageChange: PropTypes.func.isRequired,
};

export default ContentTable;
