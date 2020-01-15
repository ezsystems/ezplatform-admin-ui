import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzWidgetButton from '../base/ez-widgetbutton';

export default class EzBtnAttributesUpdate extends EzWidgetButton {
    constructor(props) {
        super(props);

        this.updateValue = this.updateValue.bind(this);
        this.updateCheckboxValue = this.updateCheckboxValue.bind(this);
        this.udpateSelecValue = this.udpateSelecValue.bind(this);
        this.renderAttribute = this.renderAttribute.bind(this);
        this.saveValues = this.saveValues.bind(this);
        this.updateClassesValue = this.updateClassesValue.bind(this);

        this.state = {
            attributesValues: props.attributesValues,
            classesValue: props.classesValue,
        };
    }

    renderString(attrName, config, value) {
        return (
            <div className="ez-ae-attribute__wrapper">
                <label className="ez-ae-attribute__label form-control-label">{config.label}</label>
                <input
                    type="text"
                    defaultValue={config.defaultValue}
                    required={config.required}
                    className="ez-ae-attribute__input form-control"
                    value={value}
                    onChange={this.updateValue}
                    data-attr-name={attrName}
                />
            </div>
        );
    }

    renderCheckbox(attrName, config, value) {
        return (
            <div className="ez-ae-attribute__wrapper">
                <label className="ez-ae-attribute__label form-control-label">{config.label}</label>
                <input
                    type="checkbox"
                    defaultChecked={config.defaultValue}
                    required={config.required}
                    className="ez-ae-attribute__input form-control"
                    checked={value}
                    onChange={this.updateCheckboxValue}
                    data-attr-name={attrName}
                />
            </div>
        );
    }

    renderNumber(attrName, config, value) {
        return (
            <div className="ez-ae-attribute__wrapper">
                <label className="ez-ae-attribute__label form-control-label">{config.label}</label>
                <input
                    type="number"
                    defaultValue={config.defaultValue}
                    required={config.required}
                    className="ez-ae-attribute__input form-control"
                    value={value}
                    onChange={this.updateValue}
                    data-attr-name={attrName}
                />
            </div>
        );
    }

    renderSelect(attrName, config, value, updateValue = this.udpateSelecValue) {
        return (
            <div className="ez-ae-attribute__wrapper">
                <label className="ez-ae-attribute__label form-control-label">{config.label}</label>
                <select
                    className="ez-ae-attribute__input form-control"
                    value={config.multiple && value !== null ? value.split(',') : value}
                    defaultValue={
                        config.multiple && config.defaultValue !== null && config.defaultValue !== undefined
                            ? [config.defaultValue.split(',')]
                            : config.defaultValue
                    }
                    onChange={updateValue}
                    data-attr-name={attrName}
                    multiple={config.multiple}>
                    {config.choices.map(this.renderChoice)}
                </select>
            </div>
        );
    }

    renderChoice(choice) {
        return <option value={choice}>{choice}</option>;
    }

    renderAttribute([attributeName, attributeConfig]) {
        const renderMethods = this.getAttributeRenderMethods();
        const methodName = renderMethods[attributeConfig.type];
        const value = this.state.attributesValues[attributeName].value;

        return <div className="ez-ae-attribute">{this[methodName](attributeName, attributeConfig, value)}</div>;
    }

    renderClass() {
        if (!Object.keys(this.classes).length) {
            return null;
        }

        return this.renderSelect('classes', this.classes, this.state.classesValue, this.updateClassesValue);
    }

    render() {
        const saveLabel = Translator.trans(/*@Desc("Save")*/ 'custom_attributes_update_btn.save_btn.label', {}, 'alloy_editor');
        const isValid = this.isValid();

        return (
            <div className="ez-ae-attributes">
                {this.renderClass()}
                {Object.entries(this.attributes).map(this.renderAttribute)}
                <button
                    className="ez-btn-ae btn btn-secondary ez-btn-ae--attributes-save float-right"
                    onClick={this.saveValues}
                    disabled={!isValid}>
                    {saveLabel}
                </button>
            </div>
        );
    }

    isValid() {
        return Object.keys(this.attributes).every((attr) => {
            return this.attributes[attr].required ? !!this.state.attributesValues[attr].value : true;
        });
    }

    clearClasses() {
        const block = this.findSelectedBlock();

        if (!Object.keys(this.classes).length) {
            return;
        }

        const classList = block.$.classList;
        this.classes.choices.forEach(className => {
            if (classList.contains(className)) {
                classList.remove(className);
            }
        });
    }

    saveValues() {
        const block = this.findSelectedBlock();
        const { attributesValues, classesValue } = this.state;
        const { editor, cancelExclusive } = this.props;
        const nativeEditor = editor.get('nativeEditor');

        Object.entries(attributesValues).forEach(([attribute, attributeData]) => {
            block.setAttribute(`data-ezattribute-${attribute}`, attributeData.value);
        });

        this.clearClasses();

        if (classesValue) {
            block.$.classList.add(...classesValue.split(','), 'ez-classes-added');
        }

        nativeEditor.unlockSelection(true);
        nativeEditor.fire('customUpdate');

        cancelExclusive();
    }

    getSelectedOptions(options) {
        return options
            .filter(({ selected }) => selected)
            .map(({ value }) => value)
            .join();
    }

    updateClassesValue({ target }) {
        const classesValue = this.getSelectedOptions([...target.options]);

        this.setState({ classesValue });
    }

    udpateSelecValue({ target }) {
        const selectedValues = this.getSelectedOptions([...target.options]);

        this.setAttributesValues(target.dataset.attrName, selectedValues);
    }

    updateCheckboxValue({ target }) {
        this.setAttributesValues(target.dataset.attrName, target.checked);
    }

    updateValue({ target }) {
        this.setAttributesValues(target.dataset.attrName, target.value);
    }

    setAttributesValues(attrName, value) {
        const attributesValues = Object.assign({}, this.state.attributesValues);

        attributesValues[attrName].value = value;

        this.setState({
            attributesValues: attributesValues,
        });
    }

    getAttributeRenderMethods() {
        return {
            string: 'renderString',
            boolean: 'renderCheckbox',
            number: 'renderNumber',
            choice: 'renderSelect',
        };
    }
}

eZ.addConfig('ezAlloyEditor.ezBtnAttributesUpdate', EzBtnAttributesUpdate);

EzBtnAttributesUpdate.propTypes = {
    editor: PropTypes.object.isRequired,
    label: PropTypes.string.isRequired,
    tabIndex: PropTypes.number.isRequired,
};
