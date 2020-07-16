import React, { Component, createRef } from 'react';
import { createPortal } from 'react-dom';
import PropTypes from 'prop-types';

import TableViewItemComponent from './table.view.item.component';
import TableViewColumnsTogglerComponent from './table.view.columns.toggler';
import ThreeStateCheckboxComponent from '../three-state-checkbox/three.state.checkbox.component';
import LanguageSelector from '../sub-items-list/language.selector.compoment';

const ACTION_COTAINER_SELECTOR = '.ez-extra-actions-container';
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
const TABLE_CELL_CLASS = 'c-table-view__cell';
const TABLE_HEAD_CLASS = `${TABLE_CELL_CLASS} ${TABLE_CELL_CLASS}--head`;
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

        this._refColumnsTogglerButton = createRef();

        this.state = {
            columnsVisibility: this.getColumnsVisibilityFromLocalStorage(),
            languageSelectorData: {},
            languageSelectorOpen: false,
        };
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
        const { columnsVisibility } = this.state;
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
            />
        );
    }

    renderBasicColumnsHeader() {
        const { sortClause, sortOrder, onSortChange } = this.props;
        const { columnsVisibility } = this.state;
        const columnsToRender = {
            name: true,
            ...columnsVisibility,
        };

        return Object.entries(columnsToRender).map(([columnKey, isVisible]) => {
            if (!isVisible) {
                return null;
            }

            let className = TABLE_HEAD_CLASS;
            let onClick = null;

            if (columnKey in SORTKEY_MAP) {
                className += ` ${TABLE_CELL_CLASS}--sortable`;

                if (SORTKEY_MAP[columnKey] === sortClause) {
                    className += sortOrder === 'ascending' ? ` ${TABLE_CELL_CLASS}--sorted-asc` : ` ${TABLE_CELL_CLASS}--sorted-desc`;
                }

                onClick = () => {
                    onSortChange(SORTKEY_MAP[columnKey]);
                };
            }

            if (columnKey === 'name') {
                className += ' c-table-view-item__cell--name';
            }

            return (
                <th key={columnKey} className={className} onClick={onClick} tabIndex={-1}>
                    <span className="c-table-view__label">{headerLabels[columnKey]}</span>
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

        const { columnsVisibility } = this.state;
        const { selectedLocationsIds, items } = this.props;
        const anyLocationSelected = !!selectedLocationsIds.size;
        const allLocationsSelected = selectedLocationsIds.size === items.length;
        const isCheckboxIndeterminate = anyLocationSelected && !allLocationsSelected;

        return (
            <thead className="c-table-view__head">
                <tr className="c-table-view__row">
                    <th className={`${TABLE_HEAD_CLASS} ${TABLE_CELL_CLASS}--checkbox`}>
                        <ThreeStateCheckboxComponent
                            indeterminate={isCheckboxIndeterminate}
                            checked={anyLocationSelected}
                            onClick={this.selectAll} // We need onClick, because MS Edge does not trigger onChange when checkbox has indeterminate state. (ref: https://stackoverflow.com/a/33529024/5766602)
                            onChange={() => {}} // Dummy callback to not trigger React warning as we cannot use onChange on MS Edge
                            class="ez-input ez-input--checkbox ez-input--no-select-border"
                        />
                    </th>
                    <th className={`${TABLE_HEAD_CLASS} ${TABLE_CELL_CLASS}--icon`} />
                    {this.renderBasicColumnsHeader()}
                    <th className={`${TABLE_HEAD_CLASS} ${TABLE_CELL_CLASS}--actions`}>
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
                <div className="c-table-view__scroller">
                    <table className="c-table-view">
                        {this.renderHead()}
                        <tbody className="c-table-view__body">{renderedItems}</tbody>
                    </table>
                </div>
                {createPortal(
                    <LanguageSelector
                        isOpen={this.state.languageSelectorOpen}
                        close={this.closeLanguageSelector}
                        {...this.state.languageSelectorData}
                    />,
                    window.document.querySelector(ACTION_COTAINER_SELECTOR)
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
};
