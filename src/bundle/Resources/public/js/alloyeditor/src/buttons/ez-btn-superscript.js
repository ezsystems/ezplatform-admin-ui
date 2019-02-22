import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnSuperscript extends AlloyEditor.ButtonSuperscript {
    static get key() {
        return 'ezsuperscript';
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const cssClass = 'ae-button ez-btn-ae ' + this.getStateClasses();

        return (
            <button
                aria-label={AlloyEditor.Strings.superscript}
                aria-pressed={cssClass.indexOf('pressed') !== -1}
                className={cssClass}
                data-type="button-superscript"
                onClick={this.execCommand}
                tabIndex={this.props.tabIndex}
                title={AlloyEditor.Strings.superscript}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#superscript" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnSuperscript.key] = AlloyEditor.EzBtnSuperscript = EzBtnSuperscript;
eZ.addConfig('ezAlloyEditor.ezBtnSuperscript', EzBtnSuperscript);
