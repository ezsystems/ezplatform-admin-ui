import React, { Component, createRef } from 'react';
import PropTypes from 'prop-types';

import Icon from '../../../common/icon/icon';
import TableViewColumnsTogglerListElement from './table.view.columns.toggler.list.element';
import { headerLabels } from './table.view.component';

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
        const panelAttrs = { className: 'c-table-view-columns-toggler__panel', ref: this._refPanel };

        if (buttonBottomDocumentOffset < panelHeight) {
            panelAttrs.className = `${panelAttrs.className} ${panelAttrs.className}--above-btn`;
        }

        return (
            <div {...panelAttrs}>
                <ul className="c-table-view-columns-toggler__list">
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
            </div>
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
                    className="c-action-btn c-table-view-columns-toggler__btn"
                    onClick={this.togglePanel}>
                    <Icon name="filters" extraClasses="ez-icon--small ez-icon--base-light" />
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
