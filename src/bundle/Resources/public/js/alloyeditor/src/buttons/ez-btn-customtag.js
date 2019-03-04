import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzWidgetButton from '../base/ez-widgetbutton';

export default class EzBtnCustomTag extends EzWidgetButton {
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

            return <ButtonComponent createNewTag="true" values={this.values} {...this.props} />;
        }

        const css = `ae-button ez-btn-ae ez-btn-ae--${this.customTagName}`;

        return (
            <button className={css} onClick={this.props.requestExclusive} tabIndex={this.props.tabIndex} title={this.label}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref={this.icon} />
                </svg>
            </button>
        );
    }
}

eZ.addConfig('ezAlloyEditor.ezBtnCustomTag', EzBtnCustomTag);

EzBtnCustomTag.propTypes = {
    editor: PropTypes.object.isRequired,
    label: PropTypes.string.isRequired,
    tabIndex: PropTypes.number.isRequired,
};
