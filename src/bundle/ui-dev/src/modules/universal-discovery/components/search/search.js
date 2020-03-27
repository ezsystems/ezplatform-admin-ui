import React, { useState, useEffect, useReducer, useContext, createContext } from 'react';
import PropTypes from 'prop-types';

export const SelectedContentTypesContext = createContext();
export const SelectedSectionContext = createContext();
export const SelectedSubtreeContext = createContext();

import Icon from '../../../common/icon/icon';
import ContentTable from '../content-table/content.table';
import Filters from '../filters/filters';
import { useSearchByQueryFetch } from '../../hooks/useSearchByQueryFetch';
import { AllowedContentTypesContext } from '../../universal.discovery.module';

const ENTER_CHAR_CODE = 13;

const selectedContentTypesReducer = (state, action) => {
    switch (action.type) {
        case 'ADD_CONTENT_TYPE':
            return [...state, action.contentTypeIdentifier];
        case 'REMOVE_CONTENT_TYPE':
            return state.filter((contentTypeIdentifier) => contentTypeIdentifier !== action.contentTypeIdentifier);
        case 'CLEAR_CONTENT_TYPES':
            return [];
        default:
            throw new Error();
    }
};

const Search = ({ itemsPerPage }) => {
    const allowedContentTypes = useContext(AllowedContentTypesContext);
    const [searchText, setSearchText] = useState('');
    const [offset, setOffset] = useState(0);
    const [filtersCollapsed, setFiltersCollapsed] = useState(true);
    const [selectedContentTypes, dispatchSelectedContentTypesAction] = useReducer(selectedContentTypesReducer, []);
    const [selectedSection, setSelectedSection] = useState('');
    const [selectedSubtree, setSelectedSubtree] = useState('');
    const [isLoading, data, searchByQuery] = useSearchByQueryFetch();
    const updateSearchQuery = ({ target: { value } }) => setSearchText(value);
    const search = (forcedOffset) => {
        if (!searchText) {
            return;
        }

        if (forcedOffset !== undefined && forcedOffset !== offset) {
            setOffset(forcedOffset);

            return;
        }

        const contentTypes = !!selectedContentTypes.length ? [...selectedContentTypes] : allowedContentTypes;

        searchByQuery(searchText, contentTypes, selectedSection, selectedSubtree, itemsPerPage, offset);
    };
    const handleKeyPressed = ({ charCode }) => {
        if (charCode === ENTER_CHAR_CODE) {
            search(0);
        }
    };
    const changePage = (pageIndex) => setOffset(pageIndex * itemsPerPage);
    const toggleFiltersCollapsed = () => setFiltersCollapsed((prevState) => !prevState);
    const renderSearchResults = () => {
        const searchResultsLabel = Translator.trans(/*@Desc("Search results")*/ 'search.search_results', {}, 'universal_discovery_widget');
        const noResultsLabel = Translator.trans(
            /*@Desc("Sorry, no results were found for")*/ 'search.no_results',
            {},
            'universal_discovery_widget'
        );
        const tipsLabel = Translator.trans(/*@Desc("Some helpful search tips")*/ 'search.tips', {}, 'universal_discovery_widget');
        const checkSpellingLabel = Translator.trans(
            /*@Desc("Check spelling of keywords.")*/ 'search.check_spelling',
            {},
            'universal_discovery_widget'
        );
        const differentKeywordsLabel = Translator.trans(
            /*@Desc("Try different keywords.")*/ 'search.different_keywords',
            {},
            'universal_discovery_widget'
        );
        const moreGeneralLabel = Translator.trans(
            /*@Desc("Try more general keywords.")*/ 'search.more_general',
            {},
            'universal_discovery_widget'
        );
        const fewerKeywordsLabel = Translator.trans(
            /*@Desc("Try fewer keywords. Reducing keywords result in more matches.")*/ 'search.fewer_keywords',
            {},
            'universal_discovery_widget'
        );
        const title = `${searchResultsLabel} (${data.count})`;

        if (data.count) {
            return (
                <ContentTable
                    count={data.count}
                    items={data.items}
                    itemsPerPage={itemsPerPage}
                    activePageIndex={offset ? offset / itemsPerPage : 0}
                    title={title}
                    onPageChange={changePage}
                />
            );
        } else if (!!data.items) {
            return (
                <div className="c-search__no-results">
                    <div className="c-search__no-results-title">{title}</div>
                    <table className="table">
                        <tbody>
                            <tr>
                                <td>
                                    <span>{`${noResultsLabel} "${searchText}".`}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h6>{tipsLabel}:</h6>
                    <ul>
                        <li>{checkSpellingLabel}</li>
                        <li>{differentKeywordsLabel}</li>
                        <li>{moreGeneralLabel}</li>
                        <li>{fewerKeywordsLabel}</li>
                    </ul>
                </div>
            );
        }
    };

    useEffect(search, [offset]);

    return (
        <div className="c-search">
            <div className="c-search__tools-wrapper">
                <div className="c-search__input-wrapper">
                    <input
                        type="search"
                        className="c-search__input form-control"
                        onChange={updateSearchQuery}
                        onKeyPress={handleKeyPressed}
                        value={searchText}
                    />
                    <button className="c-search__search-btn btn btn-primary" onClick={search.bind(this, 0)}>
                        <Icon name="search" extraClasses="ez-icon--small-medium ez-icon--light" />
                        Search
                    </button>
                </div>
                <div className="c-search__filters-btn-wrapper">
                    <button className="c-search__toggle-filters-btn btn btn-dark" onClick={toggleFiltersCollapsed}>
                        <Icon name="filters" extraClasses="ez-icon--small-medium ez-icon--light" />
                        Filters
                    </button>
                </div>
            </div>
            <SelectedContentTypesContext.Provider value={[selectedContentTypes, dispatchSelectedContentTypesAction]}>
                <SelectedSectionContext.Provider value={[selectedSection, setSelectedSection]}>
                    <SelectedSubtreeContext.Provider value={[selectedSubtree, setSelectedSubtree]}>
                        <Filters isCollapsed={filtersCollapsed} search={search} />
                    </SelectedSubtreeContext.Provider>
                </SelectedSectionContext.Provider>
            </SelectedContentTypesContext.Provider>
            {renderSearchResults()}
        </div>
    );
};

Search.propTypes = {
    itemsPerPage: PropTypes.number,
};

Search.defaultProps = {
    itemsPerPage: 50,
};

export default Search;
