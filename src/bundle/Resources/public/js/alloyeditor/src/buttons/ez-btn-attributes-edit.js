import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzWidgetButton from '../base/ez-widgetbutton';

export default class EzBtnAttributesEdit extends EzWidgetButton {
    constructor(props) {
        super(props);

        this.setDefaultAttributesMethods = {
            tr: this.setDefaultAttributesOnTableRows,
            td: this.setDefaultAttributesOnTableCells,
            li: this.setDefaultAttributesOnListItems,
        };

        this.setDefaultClassesMethods = {
            tr: this.setDefaultClassesOnTableRows,
            td: this.setDefaultClassesOnTableCells,
            li: this.setDefaultClassesOnListItems,
        };
    }
    componentDidMount() {
        const block = this.findSelectedBlock();

        this.setDefaultClasses(block);
        this.setDefaultAttributes(block);
    }

    setDefaultClasses(block) {
        if (!Object.keys(this.classes).length || block.$.classList.contains('ez-classes-added') || !this.classes.defaultValue) {
            return;
        }

        const defaultValue = this.classes.defaultValue.split(',');
        const setDefaultClassesMethod = this.setDefaultClassesMethods[this.toolbarName]
            ? this.setDefaultClassesMethods[this.toolbarName]
            : this.setDefaultClassesOnBlock;

        setDefaultClassesMethod(block, defaultValue);
    }

    setDefaultClassesOnBlock(block, value) {
        block.$.classList.add(...value);
    }

    setDefaultClassesOnTableRows(block, value) {
        const rows = block.$.closest('table').querySelectorAll('tr');

        rows.forEach((row) => row.$.classList.add(...value));
    }

    setDefaultClassesOnTableCells(block, value) {
        const cells = block.$.closest('table').querySelectorAll('td');

        cells.forEach((cell) => cell.$.classList.add(...value));
    }

    setDefaultClassesOnListItems(block, value) {
        const list = block.$.closest('ul') || block.$.closest('ol');
        const listItems = list.querySelectorAll('li');

        listItems.forEach((listItem) => listItem.$.classList.add(...value));
    }

    setDefaultAttributes(block) {
        Object.entries(this.attributes).forEach(([attributeName, config]) => {
            const attributeValue = block.getAttribute(`data-ez-attribute-${attributeName}`);

            if (attributeValue !== null) {
                return;
            }

            const defaultValue = config.defaultValue;

            if (defaultValue !== undefined) {
                const setDefaultAttributesMethod = this.setDefaultAttributesMethods[this.toolbarName]
                    ? this.setDefaultAttributesMethods[this.toolbarName]
                    : this.setDefaultAttributesOnBlock;

                setDefaultAttributesMethod(block, attributeName, defaultValue);
            }
        });
    }

    setDefaultAttributesOnBlock(block, attributeName, value) {
        block.setAttribute(`data-ez-attribute-${attributeName}`, value);
    }

    setDefaultAttributesOnTableRows(block, attributeName, value) {
        const rows = block.$.closest('table').querySelectorAll('tr');

        rows.forEach((row) => row.setAttribute(`data-ez-attribute-${attributeName}`, value));
    }

    setDefaultAttributesOnTableCells(block, attributeName, value) {
        const cells = block.$.closest('table').querySelectorAll('td');

        cells.forEach((cell) => cell.setAttribute(`data-ez-attribute-${attributeName}`, value));
    }

    setDefaultAttributesOnListItems(block, attributeName, value) {
        const list = block.$.closest('ul') || block.$.closest('ol');
        const listItems = list.querySelectorAll('li');

        listItems.forEach((listItem) => listItem.setAttribute(`data-ez-attribute-${attributeName}`, value));
    }

    getAttributesValues() {
        return Object.entries(this.attributes).reduce((total, [attributeName, config]) => {
            const block = this.findSelectedBlock();
            const defaultValue = config.defaultValue;
            let value = block.getAttribute(`data-ez-attribute-${attributeName}`);
            const isValueDefined = value !== null;

            if (!isValueDefined && defaultValue !== undefined) {
                value = defaultValue;
            }

            if (config.type === 'boolean' && isValueDefined) {
                value = value === 'true';
            }

            total[attributeName] = { value };

            return total;
        }, {});
    }

    getClassesValue() {
        const block = this.findSelectedBlock();

        return block.$.classList.value.split(' ').join();
    }

    getUpdateBtnName() {
        return `ezBtn${this.toolbarName.charAt(0).toUpperCase() + this.toolbarName.slice(1)}Update`;
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        if (this.props.renderExclusive) {
            const buttonName = this.getUpdateBtnName();
            const ButtonComponent = AlloyEditor[buttonName];

            return <ButtonComponent attributesValues={this.getAttributesValues()} classesValue={this.getClassesValue()} {...this.props} />;
        }

        const css = `ae-button ez-btn-ae ez-btn-ae--${this.toolbarName}-edit`;

        return (
            <button className={css} onClick={this.props.requestExclusive} tabIndex={this.props.tabIndex}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#edit" />
                </svg>
            </button>
        );
    }
}

eZ.addConfig('ezAlloyEditor.ezBtnAttributesEdit', EzBtnAttributesEdit);

EzBtnAttributesEdit.propTypes = {
    editor: PropTypes.object.isRequired,
    label: PropTypes.string.isRequired,
    tabIndex: PropTypes.number.isRequired,
};
