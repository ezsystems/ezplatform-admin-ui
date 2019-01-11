import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnLink extends AlloyEditor.ButtonLink {
    static get key() {
        return 'ezlink';
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const cssClass = 'ae-button ez-btn-ae ' + this.getStateClasses();

        if (this.props.renderExclusive) {
            const props = this.mergeButtonCfgProps();

            return <AlloyEditor.ButtonLinkEdit {...props} />;
        }

        return (
            <button
                aria-label={AlloyEditor.Strings.link}
                className={cssClass}
                data-type="button-link"
                onClick={this._requestExclusive}
                tabIndex={this.props.tabIndex}
                title={AlloyEditor.Strings.link}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#link" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnLink.key] = AlloyEditor.EzBtnLink = EzBtnLink;
eZ.addConfig('ezAlloyEditor.ezBtnLink', EzBtnLink);
