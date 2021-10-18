import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import Icon from '../../../common/icon/icon';
import { createCssClassNames } from '../../../common/helpers/css.class.names';

const { formatShortDateTime } = window.eZ.helpers.timezone;

export default class TableViewItemComponent extends PureComponent {
    constructor(props) {
        super(props);

        this.storePriorityValue = this.storePriorityValue.bind(this);
        this.enablePriorityInput = this.enablePriorityInput.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleCancel = this.handleCancel.bind(this);
        this.handleEdit = this.handleEdit.bind(this);
        this.onSelectCheckboxChange = this.onSelectCheckboxChange.bind(this);
        this.setPriorityInputRef = this.setPriorityInputRef.bind(this);
        this.getLanguageSelectorData = this.getLanguageSelectorData.bind(this);
        this.editItem = this.editItem.bind(this);

        this._refPriorityInput = null;

        this.state = {
            priorityValue: props.item.priority,
            priorityInputEnabled: false,
            startingPriorityValue: props.item.priority,
        };

        this.columnsRenderers = {
            name: this.renderNameCell.bind(this),
            modified: this.renderModifiedCell.bind(this),
            'content-type': this.renderContentTypeCell.bind(this),
            priority: this.renderPriorityCell.bind(this),
            translations: this.renderTranslationsCell.bind(this),
            visibility: this.renderVisibilityCell.bind(this),
            creator: this.renderCreatorCell.bind(this),
            contributor: this.renderContributorCell.bind(this),
            published: this.renderPublishedCell.bind(this),
            section: this.renderSectionCell.bind(this),
            'location-id': this.renderLocationIdCell.bind(this),
            'location-remote-id': this.renderLocationRemoteIdCell.bind(this),
            'object-id': this.renderObjectIdCell.bind(this),
            'object-remote-id': this.renderObjectRemoteIdCell.bind(this),
        };
    }

    /**
     * Enables priority input field
     *
     * @method enablePriorityInput
     * @memberof TableViewItemComponent
     */
    enablePriorityInput() {
        this.setState(() => ({ priorityInputEnabled: true }));
    }

    /**
     * Handles priority update cancel action.
     * Restores previous value and blocks the priority input.
     *
     * @method handleCancel
     * @param {Event} event
     * @memberof TableViewItemComponent
     */
    handleCancel(event) {
        event.preventDefault();

        this.setState((state) => ({
            priorityInputEnabled: false,
            priorityValue: state.startingPriorityValue,
        }));
    }

    /**
     * Handles submit action.
     * Updates priority value.
     *
     * @method handleSubmit
     * @param {Event} event
     * @memberof TableViewItemComponent
     */
    handleSubmit(event) {
        event.preventDefault();

        this.props.onItemPriorityUpdate({
            pathString: this.props.item.pathString,
            priority: this._refPriorityInput.value,
        });

        this.setState(() => ({
            priorityValue: this._refPriorityInput.value,
            priorityInputEnabled: false,
            startingPriorityValue: this._refPriorityInput.value,
        }));
    }

    /**
     * Stores priority value
     *
     * @method storePriorityValue
     * @param {Event} event
     * @memberof TableViewItemComponent
     */
    storePriorityValue(event) {
        event.preventDefault();

        this.setState(() => ({ priorityValue: this._refPriorityInput.value }));
    }

    /**
     * Edit sub item
     *
     * @method editItem
     * @memberof TableViewItemComponent
     */
    editItem(languageCode) {
        const { id, currentVersion } = this.props.item.content._info;

        this.props.handleEditItem(
            {
                _id: id,
                mainLanguageCode: languageCode,
                CurrentVersion: {
                    Version: {
                        VersionInfo: {
                            versionNo: currentVersion.versionNumber,
                        },
                    },
                },
            },
            this.props.item.id
        );
    }

    /**
     * Handles edit action.
     *
     * @method handleEdit
     * @memberof TableViewItemComponent
     */
    handleEdit() {
        const { mainLanguageCode, currentVersion } = this.props.item.content._info;
        const { languageCodes } = currentVersion;

        if (languageCodes.length > 1) {
            this.props.setLanguageSelectorData(this.getLanguageSelectorData());
            this.props.openLanguageSelector();
        } else {
            this.editItem(mainLanguageCode);
        }
    }

    setPriorityInputRef(ref) {
        this._refPriorityInput = ref;
    }

    renderNameCell() {
        const { item, generateLink } = this.props;
        const contentName = item.content._name;
        const contentTypeIdentifier = item.content._info.contentType.identifier;
        const contentTypeIconUrl = eZ.helpers.contentType.getContentTypeIconUrl(contentTypeIdentifier);
        const linkAttrs = {
            className: 'c-table-view-item__link c-table-view-item__text-wrapper',
            href: generateLink(item.id, item.content._info.id),
        };

        return (
            <span className="c-table-view-item__icon-with-name-wrapper">
                <Icon customPath={contentTypeIconUrl} extraClasses="ibexa-icon--small" />
                <a {...linkAttrs}>{contentName}</a>
            </span>
        );
    }

    /**
     * Renders a priority cell with input field
     *
     * @method renderPriorityCell
     * @returns {JSX.Element}
     * @memberof TableViewItemComponent
     */
    renderPriorityCell() {
        const inputAttrs = {
            type: 'number',
            defaultValue: this.state.priorityValue,
            onChange: this.storePriorityValue,
        };
        const priorityWrapperAttrs = {};
        const innerWrapperAttrs = {};

        if (!this.state.priorityInputEnabled) {
            inputAttrs.readOnly = true;
            delete inputAttrs.defaultValue;
            inputAttrs.value = this.state.priorityValue;
            priorityWrapperAttrs.onClick = this.enablePriorityInput;
            innerWrapperAttrs.hidden = true;
        }

        return (
            <div className="c-table-view-item__priority-wrapper" {...priorityWrapperAttrs}>
                <div className="c-table-view-item__inner-wrapper c-table-view-item__inner-wrapper--input">
                    <input
                        className="ibexa-input ibexa-input--text ibexa-input--small c-table-view-item__priority-value ibexa-input"
                        ref={this.setPriorityInputRef}
                        {...inputAttrs}
                    />
                </div>
                <div className="c-table-view-item__priority-actions" {...innerWrapperAttrs}>
                    <button
                        type="button"
                        className="btn ibexa-btn ibexa-btn--primary ibexa-btn--no-text ibexa-btn--small c-table-view-item__btn c-table-view-item__btn--submit"
                        onClick={this.handleSubmit}>
                        <Icon name="checkmark" extraClasses="ibexa-icon--small" />
                    </button>
                    <button
                        type="button"
                        className="btn ibexa-btn ibexa-btn--secondary ibexa-btn--no-text ibexa-btn--small"
                        onClick={this.handleCancel}>
                        <Icon name="discard" extraClasses="ibexa-icon--small" />
                    </button>
                </div>
            </div>
        );
    }

    renderModifiedCell() {
        const { modificationDate } = this.props.item.content._info;

        return <div className="c-table-view-item__text-wrapper">{formatShortDateTime(new Date(modificationDate.timestamp * 1000))}</div>;
    }

    renderPublishedCell() {
        const { publishedDate } = this.props.item.content._info;

        return <div className="c-table-view-item__text-wrapper">{formatShortDateTime(new Date(publishedDate.timestamp * 1000))}</div>;
    }

    renderContentTypeCell() {
        const contentTypeName = this.props.item.content._info.contentType.name;

        return <div className="c-table-view-item__text-wrapper">{contentTypeName}</div>;
    }

    renderTranslationsCell() {
        const { item, languages } = this.props;

        return (
            <>
                {item.content._info.currentVersion.languageCodes.map((languageCode) => (
                    <span key={languageCode} className="c-table-view-item__translation">
                        {languages.mappings[languageCode].name}
                    </span>
                ))}
            </>
        );
    }

    renderVisibilityCell() {
        const { invisible, hidden } = this.props.item;
        const visibleLabel = Translator.trans(/*@Desc("Visible")*/ 'items_table.row.visible.label', {}, 'sub_items');
        const notVisibleLabel = Translator.trans(/*@Desc("Not Visible")*/ 'items_table.row.not_visible.label', {}, 'sub_items');
        const label = !invisible && !hidden ? visibleLabel : notVisibleLabel;

        return <div className="c-table-view-item__text-wrapper">{label}</div>;
    }

    renderCreatorCell() {
        return <div className="c-table-view-item__text-wrapper">{this.getName(this.props.item.content._info.owner)}</div>;
    }

    renderContributorCell() {
        return <div className="c-table-view-item__text-wrapper">{this.getName(this.props.item.content._info.currentVersion.creator)}</div>;
    }

    renderSectionCell() {
        return <div className="c-table-view-item__text-wrapper">{this.getName(this.props.item.content._info.section)}</div>;
    }

    renderLocationIdCell() {
        return <div className="c-table-view-item__text-wrapper">{this.props.item.id}</div>;
    }

    renderLocationRemoteIdCell() {
        return <div className="c-table-view-item__text-wrapper">{this.props.item.remoteId}</div>;
    }

    renderObjectIdCell() {
        return <div className="c-table-view-item__text-wrapper">{this.props.item.content._info.id}</div>;
    }

    renderObjectRemoteIdCell() {
        return <div className="c-table-view-item__text-wrapper">{this.props.item.content._info.remoteId}</div>;
    }

    renderBasicColumns() {
        const { columnsVisibility, showScrollShadowLeft } = this.props;
        const columnsToRender = {
            name: true,
            ...columnsVisibility,
        };

        return Object.entries(columnsToRender).map(([columnKey, isVisible]) => {
            if (!isVisible) {
                return null;
            }

            const isNameColumn = columnKey === 'name';
            const className = createCssClassNames({
                'ibexa-table__cell': true,
                'c-table-view-item__cell': true,
                [`c-table-view-item__cell--${columnKey}`]: true,
                'c-table-view__cell--shadow-right': isNameColumn & showScrollShadowLeft,
                'ibexa-table__cell--close-left': isNameColumn,
            });

            return (
                <td key={columnKey} className={className}>
                    {this.columnsRenderers[columnKey]()}
                </td>
            );
        });
    }

    getName(item) {
        return item ? item.name : '';
    }

    /**
     * Calls onItemSelect callback for given item
     *
     * @param {Event} event
     */
    onSelectCheckboxChange(event) {
        const { item, onItemSelect } = this.props;
        const isSelected = event.target.checked;

        onItemSelect(item, isSelected);
    }

    /**
     * Get data for language selector
     *
     * @method getLanguageSelectorData
     * @returns {Object}
     * @memberof TableViewItemComponent
     */
    getLanguageSelectorData() {
        const languages = this.props.languages.mappings;
        const { languageCodes } = this.props.item.content._info.currentVersion;
        const label = Translator.trans(/*@Desc("Select language")*/ 'languages.modal.label', {}, 'sub_items');
        const languageItems = languageCodes.map((item) => ({
            label: languages[item].name,
            value: item,
        }));

        return {
            languageItems,
            label: `${label} (${languageItems.length})`,
            handleItemChange: this.editItem,
        };
    }

    componentDidMount() {
        eZ.helpers.table.parseCheckbox('.c-table-view-item__cell .ibexa-input--checkbox', 'c-table-view-item--active');
    }

    render() {
        const { isSelected, showScrollShadowRight } = this.props;
        const editLabel = Translator.trans(/*@Desc("Edit")*/ 'edit_item_btn.label', {}, 'sub_items');
        const actionCellClassName = createCssClassNames({
            'ibexa-table__cell': true,
            'c-table-view-item__cell': true,
            'c-table-view-item__cell--actions': true,
            'c-table-view-item__cell--shadow-left': showScrollShadowRight,
        });

        return (
            <tr className="ibexa-table__row c-table-view-item">
                <td className="ibexa-table__cell c-table-view-item__cell c-table-view-item__cell--checkbox">
                    <input
                        type="checkbox"
                        className="ibexa-input ibexa-input--checkbox"
                        checked={isSelected}
                        onChange={this.onSelectCheckboxChange}
                    />
                </td>
                {this.renderBasicColumns()}
                <td className={actionCellClassName}>
                    <span
                        title={editLabel}
                        data-extra-classes="c-table-view-item__tooltip"
                        onClick={this.handleEdit}
                        className="c-table-view-item__btn c-table-view-item__btn--edit"
                        tabIndex={-1}>
                        <div className="c-table-view-item__btn-inner">
                            <Icon name="edit" extraClasses="ibexa-icon--small-medium" />
                        </div>
                    </span>
                </td>
            </tr>
        );
    }
}

TableViewItemComponent.propTypes = {
    item: PropTypes.object.isRequired,
    isSelected: PropTypes.bool.isRequired,
    onItemPriorityUpdate: PropTypes.func.isRequired,
    handleEditItem: PropTypes.func.isRequired,
    generateLink: PropTypes.func.isRequired,
    languages: PropTypes.object.isRequired,
    onItemSelect: PropTypes.func.isRequired,
    columnsVisibility: PropTypes.object.isRequired,
    showScrollShadowLeft: PropTypes.bool.isRequired,
    showScrollShadowRight: PropTypes.bool.isRequired,
};
