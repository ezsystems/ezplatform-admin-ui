import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnItalic extends AlloyEditor.ButtonItalic {
    static get key() {
        return 'ezitalic';
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
                aria-label={AlloyEditor.Strings.italic}
                aria-pressed={cssClass.indexOf('pressed') !== -1}
                className={cssClass}
                data-type="button-italic"
                onClick={this.execCommand}
                tabIndex={this.props.tabIndex}
                title={AlloyEditor.Strings.italic}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#italic" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnItalic.key] = AlloyEditor.EzBtnItalic = EzBtnItalic;
eZ.addConfig('ezAlloyEditor.ezBtnItalic', EzBtnItalic);
