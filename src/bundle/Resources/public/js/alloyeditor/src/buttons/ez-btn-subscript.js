import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnSubscript extends AlloyEditor.ButtonSubscript {
    static get key() {
        return 'ezsubscript';
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
                aria-label={AlloyEditor.Strings.subscript}
                aria-pressed={cssClass.indexOf('pressed') !== -1}
                className={cssClass}
                data-type="button-subscript"
                onClick={this.execCommand}
                tabIndex={this.props.tabIndex}
                title={AlloyEditor.Strings.subscript}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#subscript" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnSubscript.key] = AlloyEditor.EzBtnSubscript = EzBtnSubscript;
eZ.addConfig('ezAlloyEditor.ezBtnSubscript', EzBtnSubscript);
