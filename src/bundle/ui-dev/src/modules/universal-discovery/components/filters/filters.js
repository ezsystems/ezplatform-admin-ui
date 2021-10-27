import React, { useContext, useState, useEffect, useCallback, useRef } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import { SelectedContentTypesContext, SelectedSectionContext, SelectedSubtreeContext, SelectedLanguageContext } from '../search/search';
import { findLocationsById } from '../../services/universal.discovery.service';
import { RestInfoContext } from '../../universal.discovery.module';

import Dropdown from '../dropdown/dropdown';
import Collapsible from '../collapsible/collapsible';
import ContentTypeSelector from '../content-type-selector/content.type.selector';
import Icon from '../../../common/icon/icon';

const languages = Object.values(window.eZ.adminUiConfig.languages.mappings);

const Filters = ({ search }) => {
    const [selectedContentTypes, dispatchSelectedContentTypesAction] = useContext(SelectedContentTypesContext);
    const [selectedSection, setSelectedSection] = useContext(SelectedSectionContext);
    const [selectedSubtree, setSelectedSubtree] = useContext(SelectedSubtreeContext);
    const [selectedLanguage, setSelectedLanguage] = useContext(SelectedLanguageContext);
    const prevSelectedLanguage = useRef(selectedLanguage);
    const [subtreeBreadcrumbs, setSubtreeBreadcrumbs] = useState('');
    const [filtersCleared, setFiltersCleared] = useState(false);
    const restInfo = useContext(RestInfoContext);
    const updateSelectedLanguage = (value) => setSelectedLanguage(value);
    const clearFilters = () => {
        dispatchSelectedContentTypesAction({ type: 'CLEAR_CONTENT_TYPES' });
        setSelectedSection('');
        clearSelectedSubree();
        setFiltersCleared(true);
    };
    const clearSelectedSubree = () => {
        setSelectedSubtree('');
        setSubtreeBreadcrumbs('');
    };
    const wrapperClassName = createCssClassNames({
        'c-filters': true,
        'ez-filters': true,
    });
    const updateSection = (value) => setSelectedSection(value);
    const openUdw = () => {
        const udwContainer = window.document.createElement('div');
        const config = JSON.parse(window.document.querySelector('#react-udw').dataset.filterSubtreeUdwConfig);
        const closeUDW = () => {
            ReactDOM.unmountComponentAtNode(udwContainer);
            udwContainer.remove();
        };
        const onConfirm = (items) => {
            const pathString = items[0].pathString;
            const pathArray = pathString.split('/').filter((val) => val);
            const id = pathArray.splice(1, pathArray.length - 1).join();

            findLocationsById({ ...restInfo, id }, (locations) => {
                const breadcrumbs = locations.map((location) => location.ContentInfo.Content.TranslatedName).join(' / ');

                setSubtreeBreadcrumbs(breadcrumbs);
            });

            setSelectedSubtree(pathString);

            closeUDW();
        };

        window.document.body.append(udwContainer);

        const mergedConfig = {
            onConfirm,
            onCancel: closeUDW,
            tabs: window.eZ.adminUiConfig.universalDiscoveryWidget.tabs,
            title: 'Browsing content',
            ...config,
        };

        ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, mergedConfig), udwContainer);
    };
    const makeSearch = useCallback(() => search(0), [search]);
    const isApplyButtonEnabled = !!selectedContentTypes.length || !!selectedSection || !!selectedSubtree || prevSelectedLanguage.current !== selectedLanguage;
    const renderSubtreeBreadcrumbs = () => {
        if (!subtreeBreadcrumbs) {
            return null;
        }

        return (
            <div className="ibexa-tag-view-select__selected-list">
                <div className="ibexa-tag-view-select__selected-item-tag">
                    {subtreeBreadcrumbs}
                    <button
                        type="button"
                        className="btn ibexa-tag-view-select__selected-item-tag-remove-btn"
                        onClick={clearSelectedSubree}
                    >
                        <Icon name="discard" extraClasses="ibexa-icon--tiny" />
                    </button>
                </div>
            </div>
        );
    };
    const renderSelectContentButton = () => {
        const selectLabel = Translator.trans(/*@Desc("Select content")*/ 'filters.tag_view_select.select', {}, 'universal_discovery_widget');
        const changeLabel = Translator.trans(/*@Desc("Change content")*/ 'filters.tag_view_change.select', {}, 'universal_discovery_widget');


        return (
            <button
                className="ibexa-tag-view-select__btn-select-path btn ibexa-btn ibexa-btn--secondary"
                type="button"
                onClick={openUdw}
            >
                { selectedSubtree ? changeLabel : selectLabel }
            </button>
        );
    };
    const filtersLabel = Translator.trans(/*@Desc("Filters")*/ 'filters.title', {}, 'universal_discovery_widget');
    const languageLabel = Translator.trans(/*@Desc("Language")*/ 'filters.language', {}, 'universal_discovery_widget');
    const sectionLabel = Translator.trans(/*@Desc("Section")*/ 'filters.section', {}, 'universal_discovery_widget');
    const subtreeLabel = Translator.trans(/*@Desc("Subtree")*/ 'filters.subtree', {}, 'universal_discovery_widget');
    const clearLabel = Translator.trans(/*@Desc("Clear")*/ 'filters.clear', {}, 'universal_discovery_widget');
    const applyLabel = Translator.trans(/*@Desc("Apply")*/ 'filters.apply', {}, 'universal_discovery_widget');
    const languageOptions = languages.filter(((language) => language.enabled)).map((language) => ({
        value: language.languageCode,
        label: language.name,
    }));
    const sectionOptions = Object.entries(window.eZ.adminUiConfig.sections).map(([sectionIdentifier, sectionName]) => ({
        value: sectionIdentifier,
        label: sectionName,
    }));

    useEffect(() => {
        if (filtersCleared) {
            setFiltersCleared(false);
            makeSearch();
        }
    }, [filtersCleared, makeSearch]);

    return (
        <div className={wrapperClassName}>
            <div className="c-filters__header">
                <div className="c-filters__header-content">
                    {filtersLabel}
                </div>
                <div className="c-filters__header-actions">
                    <button
                        type="submit"
                        className="btn ibexa-btn ibexa-btn--secondary ibexa-btn--small ibexa-btn--apply"
                        onClick={makeSearch}
                        disabled={!isApplyButtonEnabled}
                    >
                        {applyLabel}
                    </button>
                    <button
                        className="btn ibexa-btn ibexa-btn--ghost ibexa-btn--small"
                        onClick={clearFilters}
                    >
                        {clearLabel}
                    </button>
                </div>
            </div>
            <div className="c-filters__row">
                <div className="c-filters__row-title">
                    {languageLabel}
                </div>
                <Dropdown
                    small={true}
                    onChange={updateSelectedLanguage}
                    value={selectedLanguage}
                    options={languageOptions}
                />
            </div>
            <ContentTypeSelector />
            <Collapsible title={sectionLabel}>
                <Dropdown
                    small={true}
                    onChange={updateSection}
                    value={selectedSection}
                    options={sectionOptions}
                />
            </Collapsible>
            <Collapsible title={subtreeLabel}>
                <div class="ibexa-tag-view-select">
                    {renderSubtreeBreadcrumbs()}
                    {renderSelectContentButton()}
                </div>
            </Collapsible>
        </div>
    );
};

Filters.propTypes = {
    search: PropTypes.func.isRequired,
};

export default Filters;
