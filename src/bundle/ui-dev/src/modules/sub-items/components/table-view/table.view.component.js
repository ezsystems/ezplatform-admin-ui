import React, { Component, createRef } from 'react';
import { createPortal } from 'react-dom';
import PropTypes from 'prop-types';

import TableViewItemComponent from './table.view.item.component';
import TableViewColumnsTogglerComponent from './table.view.columns.toggler';
import ThreeStateCheckboxComponent from '../three-state-checkbox/three.state.checkbox.component';
import LanguageSelector from '../sub-items-list/language.selector.compoment';
import { createCssClassNames } from '../../../common/helpers/css.class.names';

const COLUMNS_VISIBILITY_LOCAL_STORAGE_DATA_KEY = 'sub-items_columns-visibility';
const DEFAULT_COLUMNS_VISIBILITY = {
    modified: true,
    'content-type': true,
    priority: true,
    translations: true,
    visibility: true,
    creator: true,
    contributor: true,
    published: true,
    section: true,
    'location-id': true,
    'location-remote-id': true,
    'object-id': true,
    'object-remote-id': true,
};
const SORTKEY_MAP = {
    name: 'ContentName',
    modified: 'DateModified',
    priority: 'LocationPriority',
};
const TABLE_HEAD_CLASS = 'ibexa-table__header-cell c-table-view__cell c-table-view__cell--head';
export const headerLabels = {
    name: Translator.trans(/*@Desc("Name")*/ 'items_table.header.name', {}, 'sub_items'),
    modified: Translator.trans(/*@Desc("Modified")*/ 'items_table.header.modified', {}, 'sub_items'),
    'content-type': Translator.trans(/*@Desc("Content type")*/ 'items_table.header.content_type', {}, 'sub_items'),
    priority: Translator.trans(/*@Desc("Priority")*/ 'items_table.header.priority', {}, 'sub_items'),
    translations: Translator.trans(/*@Desc("Translations")*/ 'items_table.header.translations', {}, 'sub_items'),
    visibility: Translator.trans(/*@Desc("Visibility")*/ 'items_table.header.visibility', {}, 'sub_items'),
    creator: Translator.trans(/*@Desc("Creator")*/ 'items_table.header.creator', {}, 'sub_items'),
    contributor: Translator.trans(/*@Desc("Contributor")*/ 'items_table.header.contributor', {}, 'sub_items'),
    published: Translator.trans(/*@Desc("Published")*/ 'items_table.header.pubished', {}, 'sub_items'),
    section: Translator.trans(/*@Desc("Section")*/ 'items_table.header.section', {}, 'sub_items'),
    'location-id': Translator.trans(/*@Desc("Location ID")*/ 'items_table.header.location_id', {}, 'sub_items'),
    'location-remote-id': Translator.trans(/*@Desc("Location remote ID")*/ 'items_table.header.location_remote_id', {}, 'sub_items'),
    'object-id': Translator.trans(/*@Desc("Object ID")*/ 'items_table.header.object_id', {}, 'sub_items'),
    'object-remote-id': Translator.trans(/*@Desc("Object remote ID")*/ 'items_table.header.object_remote_id', {}, 'sub_items'),
};

export default class TableViewComponent extends Component {
    constructor(props) {
        super(props);

        this.renderItem = this.renderItem.bind(this);
        this.selectAll = this.selectAll.bind(this);
        this.setColumnsVisibilityInLocalStorage = this.setColumnsVisibilityInLocalStorage.bind(this);
        this.toggleColumnVisibility = this.toggleColumnVisibility.bind(this);
        this.setLanguageSelectorData = this.setLanguageSelectorData.bind(this);
        this.openLanguageSelector = this.openLanguageSelector.bind(this);
        this.closeLanguageSelector = this.closeLanguageSelector.bind(this);
        this.handleScrollerScroll = this.handleScrollerScroll.bind(this);

        this._refColumnsTogglerButton = createRef();
        this._refScroller = createRef();

        this.state = {
            columnsVisibility: this.getColumnsVisibilityFromLocalStorage(),
            languageSelectorData: {},
            languageSelectorOpen: false,
            scrollShadowLeft: false,
            scrollShadowRight: false,
        };
    }

    componentDidMount() {
        this._refScroller.current.addEventListener('scroll', this.handleScrollerScroll, false);
        window.addEventListener('resize', this.handleScrollerScroll, false);
        this.handleScrollerScroll();
    }

    componentDidUpdate(prevProps, prevState) {
        if (this.state.columnsVisibility !== prevState.columnsVisibility) {
            this.handleScrollerScroll();
        }
    }

    componentWillUnmount() {
        this._refScroller.current.removeEventListener('scroll', this.handleScrollerScroll, false);
        window.removeEventListener('resize', this.handleScrollerScroll, false);
    }

    handleScrollerScroll() {
        this.setState(() => {
            if (!this._refScroller.current) {
                return {};
            }

            const scroller = this._refScroller.current;
            const offsetRoudingCompensator = 0.5;

            return {
                scrollShadowLeft: scroller.scrollLeft > 0,
                scrollShadowRight: scroller.scrollLeft < scroller.scrollWidth - scroller.offsetWidth - 2 * offsetRoudingCompensator,
            };
        });
    }

    getColumnsVisibilityFromLocalStorage() {
        const columnsVisibilityData = localStorage.getItem(COLUMNS_VISIBILITY_LOCAL_STORAGE_DATA_KEY);
        const columnsVisibility = { ...DEFAULT_COLUMNS_VISIBILITY };

        if (columnsVisibilityData) {
            Object.entries(JSON.parse(columnsVisibilityData)).forEach(([id, isVisible]) => {
                if (id in columnsVisibility) {
                    columnsVisibility[id] = isVisible;
                }
            });
        }

        return columnsVisibility;
    }

    setColumnsVisibilityInLocalStorage() {
        const columnsVisibilityData = JSON.stringify(this.state.columnsVisibility);

        localStorage.setItem(COLUMNS_VISIBILITY_LOCAL_STORAGE_DATA_KEY, columnsVisibilityData);
    }

    /**
     * Selects all visible items
     */
    selectAll() {
        const { toggleAllItemsSelect, selectedLocationsIds } = this.props;
        const anyLocationSelected = !!selectedLocationsIds.size;
        const isSelectAction = !anyLocationSelected;

        toggleAllItemsSelect(isSelectAction);
    }

    toggleColumnVisibility(column) {
        this.setState(
            (state) => ({
                columnsVisibility: {
                    ...state.columnsVisibility,
                    [column]: !state.columnsVisibility[column],
                },
            }),
            this.setColumnsVisibilityInLocalStorage
        );
    }

    /**
     * Sets language selector data
     *
     * @param {Object} data
     */
    setLanguageSelectorData(data) {
        this.setState({ languageSelectorData: data });
    }

    /**
     * @method openLanguageSelector
     * @memberof TableViewComponent
     */
    openLanguageSelector() {
        this.setState({ languageSelectorOpen: true });
    }

    /**
     * @method closeLanguageSelector
     * @memberof TableViewComponent
     */
    closeLanguageSelector() {
        this.setState({ languageSelectorOpen: false });
    }

    /**
     * Renders single list item
     *
     * @method renderItem
     * @param {Object} item
     * @returns {JSX.Element}
     * @memberof TableViewComponent
     */
    renderItem(item) {
        const { columnsVisibility, scrollShadowLeft, scrollShadowRight } = this.state;
        const { handleItemPriorityUpdate, handleEditItem, generateLink, languages, onItemSelect, selectedLocationsIds } = this.props;
        const isSelected = selectedLocationsIds.has(item.id);

        return (
            <TableViewItemComponent
                key={item.id}
                item={item}
                onItemPriorityUpdate={handleItemPriorityUpdate}
                languages={languages}
                handleEditItem={handleEditItem}
                generateLink={generateLink}
                onItemSelect={onItemSelect}
                isSelected={isSelected}
                columnsVisibility={columnsVisibility}
                setLanguageSelectorData={this.setLanguageSelectorData}
                openLanguageSelector={this.openLanguageSelector}
                showScrollShadowLeft={scrollShadowLeft}
                showScrollShadowRight={scrollShadowRight}
            />
        );
    }

    renderBasicColumnsHeader() {
        const { sortClause, sortOrder, onSortChange } = this.props;
        const { columnsVisibility, scrollShadowLeft } = this.state;
        const columnsToRender = {
            name: true,
            ...columnsVisibility,
        };

        return Object.entries(columnsToRender).map(([columnKey, isVisible]) => {
            if (!isVisible) {
                return null;
            }

            let onClick = null;
            const isNameColumn = columnKey === 'name';
            const className = createCssClassNames({
                [TABLE_HEAD_CLASS]: true,
                'c-table-view__cell--name': isNameColumn,
                'ibexa-table__header-cell--close-left': isNameColumn,
                'c-table-view__cell--shadow-right': scrollShadowLeft && isNameColumn,
            });
            const wrapperClassName = createCssClassNames({
                'c-table-view__label': true,
                'ibexa-table__sort-column': columnKey in SORTKEY_MAP,
                'ibexa-table__header-cell-text-wrapper': true,
                'ibexa-table__sort-column--asc': SORTKEY_MAP[columnKey] === sortClause && sortOrder === 'ascending',
                'ibexa-table__sort-column--desc': SORTKEY_MAP[columnKey] === sortClause && sortOrder === 'descending',
            });

            if (columnKey in SORTKEY_MAP) {
                onClick = () => {
                    onSortChange(SORTKEY_MAP[columnKey]);
                };
            }

            return (
                <th key={columnKey} className={className} onClick={onClick} tabIndex={-1}>
                    <span className={wrapperClassName}>{headerLabels[columnKey]}</span>
                </th>
            );
        });
    }

    /**
     * Renders table's head
     *
     * @method renderHead
     * @returns {JSX.Element|null}
     * @memberof GridViewComponent
     */
    renderHead() {
        if (!this.props.items.length) {
            return null;
        }

        const { columnsVisibility, scrollShadowRight } = this.state;
        const { selectedLocationsIds, items } = this.props;
        const anyLocationSelected = !!selectedLocationsIds.size;
        const allLocationsSelected = selectedLocationsIds.size === items.length;
        const isCheckboxIndeterminate = anyLocationSelected && !allLocationsSelected;
        const togglerClassName = createCssClassNames({
            'c-table-view__cell': true,
            'c-table-view__cell--toggler': true,
            'c-table-view__cell--shadow-left': scrollShadowRight,
        });

        return (
            <thead className="c-table-view__head">
                <tr className="ibexa-table__head-row c-table-view__row">
                    <th className={`${TABLE_HEAD_CLASS} c-table-view__cell--checkbox`}>
                        <ThreeStateCheckboxComponent
                            indeterminate={isCheckboxIndeterminate}
                            checked={anyLocationSelected}
                            onClick={this.selectAll} // We need onClick, because MS Edge does not trigger onChange when checkbox has indeterminate state. (ref: https://stackoverflow.com/a/33529024/5766602)
                            onChange={() => {}} // Dummy callback to not trigger React warning as we cannot use onChange on MS Edge
                            className="ibexa-input ibexa-input--checkbox"
                        />
                    </th>
                    {this.renderBasicColumnsHeader()}
                    <th className={togglerClassName}>
                        <TableViewColumnsTogglerComponent
                            columnsVisibility={columnsVisibility}
                            toggleColumnVisibility={this.toggleColumnVisibility}
                        />
                    </th>
                </tr>
            </thead>
        );
    }

    render() {
        const { items } = this.props;
        const renderedItems = items.map(this.renderItem);

        return (
            <div className="c-table-view__wrapper">
                <div className="c-table-view__scroller" ref={this._refScroller}>
                    <table className="table ibexa-table ibexa-table--has-bulk-checkbox c-table-view">
                        {this.renderHead()}
                        <tbody className="ibexa-table__body c-table-view__body">{renderedItems}</tbody>
                    </table>
                </div>
                {createPortal(
                    <LanguageSelector
                        isOpen={this.state.languageSelectorOpen}
                        close={this.closeLanguageSelector}
                        {...this.state.languageSelectorData}
                    />,
                    window.document.querySelector(this.props.languageContainerSelector)
                )}
            </div>
        );
    }
}

TableViewComponent.propTypes = {
    items: PropTypes.arrayOf(PropTypes.object).isRequired,
    handleItemPriorityUpdate: PropTypes.func.isRequired,
    generateLink: PropTypes.func.isRequired,
    handleEditItem: PropTypes.func.isRequired,
    languages: PropTypes.object.isRequired,
    onItemSelect: PropTypes.func.isRequired,
    toggleAllItemsSelect: PropTypes.func.isRequired,
    selectedLocationsIds: PropTypes.instanceOf(Set),
    onSortChange: PropTypes.func.isRequired,
    sortClause: PropTypes.string.isRequired,
    sortOrder: PropTypes.string.isRequired,
    languageContainerSelector: PropTypes.string.isRequired,
};
