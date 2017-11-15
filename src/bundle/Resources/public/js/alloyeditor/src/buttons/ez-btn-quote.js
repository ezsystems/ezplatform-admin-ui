import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnQuote extends AlloyEditor.ButtonQuote {
    static get key() {
        return 'ezquote';
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
                aria-label={AlloyEditor.Strings.quote}
                aria-pressed={cssClass.indexOf('pressed') !== -1}
                className={cssClass} data-type="button-quote"
                onClick={this.execCommand} tabIndex={this.props.tabIndex}
                title={AlloyEditor.Strings.quote}
            >
                <span className="ae-icon-quote"></span>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnQuote.key] = AlloyEditor.EzBtnQuote = EzBtnQuote;
