import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnStrike extends AlloyEditor.ButtonStrike {
    static get key() {
        return 'ezstrike';
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
                aria-label={AlloyEditor.Strings.strike}
                aria-pressed={cssClass.indexOf('pressed') !== -1}
                className={cssClass} data-type="button-strike"
                onClick={this.execCommand} tabIndex={this.props.tabIndex}
                title={AlloyEditor.Strings.strike}
            >
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#strikethrough"></use>
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnStrike.key] = AlloyEditor.EzBtnStrike = EzBtnStrike;
