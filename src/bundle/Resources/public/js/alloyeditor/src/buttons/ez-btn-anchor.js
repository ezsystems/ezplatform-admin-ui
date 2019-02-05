import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnAnchor extends Component {
    constructor(props) {
        super(props);

        this.getStateClasses = AlloyEditor.ButtonStateClasses.getStateClasses;
    }

    static get key() {
        return 'ezanchor';
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        if (this.props.renderExclusive) {
            return <AlloyEditor.EzBtnAnchorEdit {...this.props} />;
        }

        const cssClass = `ae-button ez-btn-ae--anchor ez-btn-ae ${this.getStateClasses()}`;
        const label = Translator.trans(/*@Desc("Anchor")*/ 'anchor_btn.label', {}, 'alloy_editor');

        return (
            <button
                aria-pressed={cssClass.indexOf('pressed') !== -1}
                className={cssClass}
                onClick={this.props.requestExclusive}
                tabIndex={this.props.tabIndex}
                title={label}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#link-anchor" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnAnchor.key] = AlloyEditor.EzBtnAnchor = EzBtnAnchor;
eZ.addConfig('ezAlloyEditor.ezBtnAnchor', EzBtnAnchor);
