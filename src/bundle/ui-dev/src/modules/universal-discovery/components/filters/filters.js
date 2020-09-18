import React, { useContext, useState, useEffect, useCallback } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import { SelectedContentTypesContext, SelectedSectionContext, SelectedSubtreeContext } from '../search/search';
import { findLocationsById } from '../../services/universal.discovery.service';
import { RestInfoContext } from '../../universal.discovery.module';

import ContentTypeSelector from '../content-type-selector/content.type.selector';
import Icon from '../../../common/icon/icon';

const Filters = ({ isCollapsed, search }) => {
    const [selectedContentTypes, dispatchSelectedContentTypesAction] = useContext(SelectedContentTypesContext);
    const [selectedSection, setSelectedSection] = useContext(SelectedSectionContext);
    const [selectedSubtree, setSelectedSubtree] = useContext(SelectedSubtreeContext);
    const [subtreeBreadcrumbs, setSubtreeBreadcrumbs] = useState('');
    const [filtersCleared, setFiltersCleared] = useState(false);
    const restInfo = useContext(RestInfoContext);
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
        'ez-filters--collapsed': isCollapsed,
    });
    const updateSection = (event) => {
        const value = event.target.value;

        setSelectedSection(value);
    };
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
    const isApplyButtonEnabled = !!selectedContentTypes.length || !!selectedSection || !!selectedSubtree;
    const renderSelectContentButton = () => {
        if (selectedSubtree) {
            return null;
        }

        return (
            <button className="btn btn-secondary ez-btn--udw-select-location" type="button" onClick={openUdw}>
                Select content
            </button>
        );
    };
    const renderSubtreeBreadcrumbs = () => {
        if (!subtreeBreadcrumbs) {
            return null;
        }

        return (
            <div className="ez-tag">
                <div className="ez-tag__content">{subtreeBreadcrumbs}</div>
                <button type="button" className="ez-tag__remove-btn" onClick={clearSelectedSubree}>
                    <Icon name="discard" extraClasses="ez-icon--small ez-icon--dark" />
                </button>
            </div>
        );
    };
    const contentTypeLabel = Translator.trans(/*@Desc("Content Type")*/ 'filters.content_type', {}, 'universal_discovery_widget');
    const sectionLabel = Translator.trans(/*@Desc("Section")*/ 'filters.section', {}, 'universal_discovery_widget');
    const anySectionLabel = Translator.trans(/*@Desc("Any section")*/ 'filters.any_section', {}, 'universal_discovery_widget');
    const subtreeLabel = Translator.trans(/*@Desc("Subtree")*/ 'filters.subtree', {}, 'universal_discovery_widget');
    const clearLabel = Translator.trans(/*@Desc("Clear")*/ 'filters.clear', {}, 'universal_discovery_widget');
    const applyLabel = Translator.trans(/*@Desc("Apply")*/ 'filters.apply', {}, 'universal_discovery_widget');

    useEffect(() => {
        if (filtersCleared) {
            setFiltersCleared(false);
            makeSearch();
        }
    }, [filtersCleared, makeSearch]);

    return (
        <div className={wrapperClassName}>
            <div className="ez-filters__row">
                <div className="ez-filters__item ez-filters__item--content-type">
                    <label className="ez-label">{contentTypeLabel}</label>
                    <ContentTypeSelector />
                </div>
            </div>
            <div className="ez-filters__row">
                <div className="ez-filters__item ez-filters__item--section">
                    <label className="ez-label">{sectionLabel}</label>
                    <select className="ez-filters__select form-control" onChange={updateSection} value={selectedSection}>
                        <option value={''}>{anySectionLabel}</option>
                        {Object.entries(window.eZ.adminUiConfig.sections).map(([sectionIdentifier, sectionName]) => {
                            return (
                                <option key={sectionIdentifier} value={sectionIdentifier}>
                                    {sectionName}
                                </option>
                            );
                        })}
                    </select>
                </div>
                <div className="ez-filters__item ez-filters__item--subtree">
                    <label className="ez-label">{subtreeLabel}:</label>
                    <div>
                        {renderSelectContentButton()}
                        {renderSubtreeBreadcrumbs()}
                    </div>
                </div>
            </div>
            <div className="ez-filters__btns">
                <button type="submit" className="btn btn-primary ez-btn-apply" onClick={makeSearch} disabled={!isApplyButtonEnabled}>
                    {applyLabel}
                </button>
                <button className="ez-btn ez-btn--no-border" onClick={clearFilters}>
                    {clearLabel}
                </button>
            </div>
        </div>
    );
};

Filters.propTypes = {
    isCollapsed: PropTypes.bool.isRequired,
    search: PropTypes.func.isRequired,
};

export default Filters;
