import React, { useEffect, useRef } from 'react';
import PropTypes from 'prop-types';

import ContentTableItem from './content.table.item';

import Pagination from '../../../common/pagination/pagination';

const ContentTable = ({ count, itemsPerPage, items, activePageIndex, title, onPageChange }) => {
    const refContentTable = useRef(null);
    const nameLabel = Translator.trans(/*@Desc("Name")*/ 'content_table.name', {}, 'universal_discovery_widget');
    const modifiedLabel = Translator.trans(/*@Desc("Modified")*/ 'content_table.modified', {}, 'universal_discovery_widget');
    const contentTypeLabel = Translator.trans(/*@Desc("Content Type")*/ 'content_table.content_type', {}, 'universal_discovery_widget');

    useEffect(() => {
        window.eZ.helpers.tooltips.parse(refContentTable.current);
    }, []);

    return (
        <div className="c-content-table" ref={refContentTable}>
            <div className="c-content-table__title">{title}</div>
            <div className="c-content-table__items">
                <table className="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{nameLabel}</th>
                            <th>{modifiedLabel}</th>
                            <th>{contentTypeLabel}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
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
