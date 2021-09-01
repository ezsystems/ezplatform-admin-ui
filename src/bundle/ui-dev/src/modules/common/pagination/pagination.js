import React from 'react';
import PropTypes from 'prop-types';

import PaginationButton from './pagination.button';

const DOTS = '...';
/**
 * Computes array with pagination pages.
 *
 * Example 1: [ 1, "...", 5, 6, 7, 8, 9, 10 ] (for: proximity = 2; pagesNumber = 10; activePageIndex = 7)
 * Example 2: [ 1, "...", 3, 4, 5, 6, 7, "...", 10 ] (for: proximity = 2; pagesNumber = 10; activePageIndex = 5)
 * Example 3: [ 1, "...", 8, 9, 10, 11, 12, "...", 20 ] (for: proximity = 2; pagesNumber = 20; activePageIndex = 10)
 *
 * @param {Object} params
 * @param {Number} params.proximity
 * @param {Number} params.activePageIndex
 * @param {Number} params.pagesCount
 * @param {String} params.separator
 *
 * @returns {Array}
 */
export const computePages = ({ proximity, activePageIndex, pagesCount, separator }) => {
    const pages = [];
    let wasSeparator = false;

    for (let i = 1; i <= pagesCount; i++) {
        const isFirstPage = i === 1;
        const isLastPage = i === pagesCount;
        const isInRange = i >= activePageIndex + 1 - proximity && i <= activePageIndex + 1 + proximity;

        if (isFirstPage || isLastPage || isInRange) {
            pages.push(i);
            wasSeparator = false;
        } else if (!wasSeparator) {
            pages.push(separator);
            wasSeparator = true;
        }
    }

    return pages;
};

const Pagination = ({ totalCount, itemsPerPage, proximity, activePageIndex, onPageChange, disabled: paginationDisabled }) => {
    const pagesCount = Math.ceil(totalCount / itemsPerPage);

    if (pagesCount <= 1) {
        return null;
    }

    const previousPage = activePageIndex - 1;
    const nextPage = activePageIndex + 1;
    const isFirstPage = activePageIndex === 0;
    const isLastPage = activePageIndex + 1 === pagesCount;
    const pages = computePages({ proximity, activePageIndex, pagesCount, separator: DOTS });
    const paginationButtons = pages.map((page, index) => {
        if (page === DOTS) {
            return <PaginationButton key={`dots-${index}`} label={DOTS} disabled={true} />;
        }

        const isCurrentPage = page === activePageIndex + 1;
        const additionalClasses = isCurrentPage ? 'active' : '';
        const label = '' + page;

        return (
            <PaginationButton
                key={page}
                pageIndex={page - 1}
                label={label}
                additionalClasses={additionalClasses}
                onPageChange={onPageChange}
                disabled={paginationDisabled}
            />
        );
    });

    return (
        <ul className="c-pagination pagination ibexa-pagination__navigation">
            <PaginationButton
                pageIndex={previousPage}
                additionalClasses="prev"
                disabled={isFirstPage || paginationDisabled}
                onPageChange={onPageChange}
            />
            {paginationButtons}
            <PaginationButton
                pageIndex={nextPage}
                additionalClasses="next"
                disabled={isLastPage || paginationDisabled}
                onPageChange={onPageChange}
            />
        </ul>
    );
};

Pagination.propTypes = {
    proximity: PropTypes.number.isRequired,
    itemsPerPage: PropTypes.number.isRequired,
    activePageIndex: PropTypes.number.isRequired,
    totalCount: PropTypes.number.isRequired,
    onPageChange: PropTypes.func.isRequired,
    disabled: PropTypes.bool.isRequired,
};

export default Pagination;
