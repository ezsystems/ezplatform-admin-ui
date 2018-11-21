import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnImageLink extends AlloyEditor.ButtonLink {
    constructor(props) {
        super(props);

        this.requestExclusive = this.requestExclusive.bind(this);
        this.cancelExclusive = this.cancelExclusive.bind(this);

        this.state = {
            renderExclusive: this.getWidget().isEditingLink(),
        };
    }

    static get key() {
        return 'ezimagelink';
    }

    getWidget() {
        const editor = this.props.editor.get('nativeEditor');
        const wrapper = editor.getSelection().getStartElement();

        return editor.widgets.getByElement(wrapper);
    }

    requestExclusive() {
        const widget = this.getWidget();

        widget.setLinkEditState();
        widget.setFocused(true);

        this.setState(() => ({ renderExclusive: true }));
    }

    cancelExclusive() {
        this.setState(() => ({ renderExclusive: false }), this.props.cancelExclusive);
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const cssClass = 'ae-button ez-btn-ae ' + this.getStateClasses();

        if (this.state.renderExclusive) {
            const props = this.mergeButtonCfgProps();

            return <AlloyEditor.EzBtnImageLinkEdit {...props} cancelExclusive={this.cancelExclusive} />;
        }

        return (
            <button
                aria-label={AlloyEditor.Strings.link}
                className={cssClass}
                data-type="button-link"
                onClick={this.requestExclusive}
                tabIndex={this.props.tabIndex}
                title={AlloyEditor.Strings.link}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#link" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnImageLink.key] = AlloyEditor.EzBtnImageLink = EzBtnImageLink;
