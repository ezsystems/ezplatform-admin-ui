import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnBold extends AlloyEditor.ButtonBold {
    static get key() {
        return 'ezbold';
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
                aria-label={AlloyEditor.Strings.bold}
                aria-pressed={cssClass.indexOf('pressed') !== -1}
                className={cssClass}
                data-type="button-bold"
                onClick={this.execCommand}
                tabIndex={this.props.tabIndex}
                title={AlloyEditor.Strings.bold}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#bold" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnBold.key] = AlloyEditor.EzBtnBold = EzBtnBold;
eZ.addConfig('ezAlloyEditor.ezBtnBold', EzBtnBold);
