import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzWidgetButton from '../base/ez-widgetbutton';

export default class EzBtnCustomTagEdit extends EzWidgetButton {
    /**
     * Gets values for the configuration.
     *
     * @method getValues
     * @return {Object} The configuration values.
     */
    getValues() {
        return Object.keys(this.attributes).reduce((total, attr) => {
            let value = this.getWidget().getConfig(attr);

            if (this.attributes[attr].type === 'boolean') {
                value = value === 'true';
            }

            total[attr] = { value };

            return total;
        }, {});
    }

    getUpdateBtnName() {
        return `ezBtn${this.customTagName.charAt(0).toUpperCase() + this.customTagName.slice(1)}Update`;
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

            return <ButtonComponent values={this.getValues()} {...this.props} />;
        }

        const css = `ae-button ez-btn-ae ez-btn-ae--${this.customTagName}-edit`;

        return (
            <button className={css} onClick={this.props.requestExclusive} tabIndex={this.props.tabIndex}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#edit" />
                </svg>
            </button>
        );
    }
}

eZ.addConfig('ezAlloyEditor.ezBtnCustomTagEdit', EzBtnCustomTagEdit);

EzBtnCustomTagEdit.propTypes = {
    editor: PropTypes.object.isRequired,
    label: PropTypes.string.isRequired,
    tabIndex: PropTypes.number.isRequired,
};
