import React, { Component, createRef } from 'react';
import PropTypes from 'prop-types';

import Icon from '../../../common/icon/icon';
import TableViewColumnsTogglerListElement from './table.view.columns.toggler.list.element';
import { headerLabels } from './table.view.component';
import { createCssClassNames } from '../../../common/helpers/css.class.names';

const DEFAULT_PANEL_HEIGHT = 450;

export default class TableViewColumnsTogglerComponent extends Component {
    constructor(props) {
        super(props);

        this.togglePanel = this.togglePanel.bind(this);
        this.hidePanel = this.hidePanel.bind(this);

        this._refTogglerButton = createRef();
        this._refPanel = createRef();

        this.state = {
            isOpen: false,
            buttonBottomDocumentOffset: null,
            panelHeight: null,
        };
    }

    componentDidMount() {
        document.addEventListener('click', this.hidePanel, false);

        this.setState(() => ({
            buttonBottomDocumentOffset: this.getBtnBottomDocumentOffset(),
        }));
    }

    componentDidUpdate() {
        const { isOpen, panelHeight } = this.state;

        if (isOpen && panelHeight === null) {
            this.setState({
                panelHeight: this._refPanel.current.offsetHeight,
            });
        }
    }

    componentWillUnmount() {
        document.removeEventListener('click', this.hidePanel);
    }

    getBtnBottomDocumentOffset() {
        const buttonTopOffset = this._refTogglerButton.current.getBoundingClientRect().top + window.scrollY;

        return document.documentElement.scrollHeight - buttonTopOffset;
    }

    hidePanel({ target }) {
        if (!this.state.isOpen) {
            return;
        }

        const isClickInsideToggler = target.closest('.c-table-view-columns-toggler');

        if (!isClickInsideToggler) {
            this.setState(() => ({
                isOpen: false,
            }));
        }
    }

    togglePanel() {
        this.setState((state) => ({
            isOpen: !state.isOpen,
        }));
    }

    renderPanel() {
        if (!this.state.isOpen) {
            return null;
        }

        const { columnsVisibility, toggleColumnVisibility } = this.props;
        const { buttonBottomDocumentOffset, panelHeight: measuredPanelHeight } = this.state;
        const panelHeight = measuredPanelHeight ? measuredPanelHeight : DEFAULT_PANEL_HEIGHT;
        const showAboveBtn = buttonBottomDocumentOffset < panelHeight;
        const className = createCssClassNames({
            'ibexa-popup-menu': true,
            'c-table-view-columns-toggler__panel': true,
            'c-table-view-columns-toggler__panel--above-btn': showAboveBtn,
        });

        return (
            <ul className={className} ref={this._refPanel}>
                {Object.entries(columnsVisibility).map(([columnKey, isColumnVisible]) => {
                    const label = headerLabels[columnKey];

                    return (
                        <TableViewColumnsTogglerListElement
                            key={columnKey}
                            label={label}
                            columnKey={columnKey}
                            isColumnVisible={isColumnVisible}
                            toggleColumnVisibility={toggleColumnVisibility}
                        />
                    );
                })}
            </ul>
        );
    }

    render() {
        const filterLabel = Translator.trans(/*@Desc("Filters")*/ 'items_table.header.filters', {}, 'sub_items');

        return (
            <div className="c-table-view-columns-toggler">
                <button
                    ref={this._refTogglerButton}
                    type="button"
                    title={filterLabel}
                    className="btn ibexa-btn ibexa-btn--small ibexa-btn--ghost ibexa-btn--no-text"
                    onClick={this.togglePanel}>
                    <Icon name="filters" extraClasses="ibexa-icon--small" />
                </button>
                {this.renderPanel()}
            </div>
        );
    }
}

TableViewColumnsTogglerComponent.propTypes = {
    columnsVisibility: PropTypes.object.isRequired,
    toggleColumnVisibility: PropTypes.func.isRequired,
};
