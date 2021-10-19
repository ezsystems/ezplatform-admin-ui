import React, { useState, useEffect, useReducer, useContext, createContext, useRef } from 'react';
import PropTypes from 'prop-types';

export const SelectedContentTypesContext = createContext();
export const SelectedSectionContext = createContext();
export const SelectedSubtreeContext = createContext();

import Icon from '../../../common/icon/icon';
import InputSearch from '../input-search/input.search';
import ContentTable from '../content-table/content.table';
import Filters from '../filters/filters';
import { useSearchByQueryFetch } from '../../hooks/useSearchByQueryFetch';
import { AllowedContentTypesContext, SearchTextContext } from '../../universal.discovery.module';

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

const languages = Object.values(window.eZ.adminUiConfig.languages.mappings);

const Search = ({ itemsPerPage }) => {
    const filtersLabel = Translator.trans(/*@Desc("Filters")*/ 'search.filters', {}, 'universal_discovery_widget');
    const searchLabel = Translator.trans(/*@Desc("Search")*/ 'search.search', {}, 'universal_discovery_widget');
    const allowedContentTypes = useContext(AllowedContentTypesContext);
    const [searchText, setSearchText] = useContext(SearchTextContext);
    const [offset, setOffset] = useState(0);
    const [selectedContentTypes, dispatchSelectedContentTypesAction] = useReducer(selectedContentTypesReducer, []);
    const [selectedSection, setSelectedSection] = useState('');
    const [selectedSubtree, setSelectedSubtree] = useState('');
    const firstLanguageCode = languages.length ? languages[0].languageCode : '';
    const [selectedLanguage, setSelectedLanguage] = useState(firstLanguageCode);
    const prevSearchText = useRef(null);
    const searchActionRef = useRef(null);
    const updateSelectedLanguage = (event) => setSelectedLanguage(event.target.value);
    const [isLoading, data, searchByQuery] = useSearchByQueryFetch();
    const search = () => {
        const shouldResetOffset = prevSearchText.current !== searchText && offset !== 0;

        prevSearchText.current = searchText;

        if (!searchText) {
            return;
        }

        if (shouldResetOffset) {
            setOffset(0);

            return;
        }

        const contentTypes = !!selectedContentTypes.length ? [...selectedContentTypes] : allowedContentTypes;

        searchByQuery(searchText, contentTypes, selectedSection, selectedSubtree, itemsPerPage, offset, selectedLanguage);
    };
    const searchSubmit = () => {
        searchActionRef.current();
    }
    const changePage = (pageIndex) => setOffset(pageIndex * itemsPerPage);
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
                    <table className="table table-hover">
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

    useEffect(search, [searchText, offset]);

    return (
        <div className="c-search">
            <div className="c-search__topbar">
                <div className="c-search__input-wrapper">
                    <InputSearch small={false} ref={searchActionRef} />
                </div>
                {languages.length > 1 || true ? (
                    <div className="c-search__selector-wrapper">
                        <select
                            className="form-control c-search__select-language"
                            onChange={updateSelectedLanguage}
                            value={selectedLanguage}>
                            {languages.map((language) => {
                                if (!language.enabled) {
                                    return null;
                                }

                                return (
                                    <option key={language.id} value={language.languageCode} onChange={updateSelectedLanguage}>
                                        {language.name}
                                    </option>
                                );
                            })}
                        </select>
                    </div>
                ) : null}
                <button className="c-search__search-btn btn ibexa-btn ibexa-btn--primary" onClick={searchSubmit}>
                    {searchLabel}
                </button>
            </div>
            <div className="c-search__main">
                <div class="c-search__sidebar">
                    <SelectedContentTypesContext.Provider value={[selectedContentTypes, dispatchSelectedContentTypesAction]}>
                        <SelectedSectionContext.Provider value={[selectedSection, setSelectedSection]}>
                            <SelectedSubtreeContext.Provider value={[selectedSubtree, setSelectedSubtree]}>
                                <Filters isCollapsed={false} search={search} />
                            </SelectedSubtreeContext.Provider>
                        </SelectedSectionContext.Provider>
                    </SelectedContentTypesContext.Provider>
                </div>
                <div class="c-search__xontent">
                    {renderSearchResults()}
                </div>
            </div>
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
