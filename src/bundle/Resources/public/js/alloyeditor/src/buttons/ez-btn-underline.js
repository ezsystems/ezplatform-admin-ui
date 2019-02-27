import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnUnderline extends AlloyEditor.ButtonUnderline {
    static get key() {
        return 'ezunderline';
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
                aria-label={AlloyEditor.Strings.underline}
                aria-pressed={cssClass.indexOf('pressed') !== -1}
                className={cssClass}
                data-type="button-underline"
                onClick={this.execCommand}
                tabIndex={this.props.tabIndex}
                title={AlloyEditor.Strings.underline}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#underscore" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnUnderline.key] = AlloyEditor.EzBtnUnderline = EzBtnUnderline;
eZ.addConfig('ezAlloyEditor.ezBtnUnderline', EzBtnUnderline);
