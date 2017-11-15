import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzEmbedDiscoverContentButton from '../base/ez-embeddiscovercontent';

export default class EzBtnEmbedUpdate extends EzEmbedDiscoverContentButton {
    static get key() {
        return 'ezembedupdate';
    }

    /**
     * Updates the emebed element with the selected content in UDW.
     *
     * @method updateEmbed
     * @param {EventFacade} e the contentDiscovered event facade
     * @protected
     */
    updateEmbed(items) {
        const contentId = items[0].ContentInfo.Content._id;
        const widget = this.getWidget();

        this.setContentInfo(contentId);
        widget.focus();
        widget.setWidgetContent("");
        widget.renderEmbedPreview(items[0].ContentInfo.Content.Name);

        document.querySelector('#react-udw').innerHTML = '';
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const css = 'ae-button ez-btn-ae ez-btn-ae--embedupdate ' + this.getStateClasses();

        return (
            <button className={css} onClick={this.chooseContent.bind(this)} tabIndex={this.props.tabIndex}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#tag"></use>
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnEmbedUpdate.key] = AlloyEditor.EzBtnEmbedUpdate = EzBtnEmbedUpdate;

EzBtnEmbedUpdate.defaultProps = {
    udwTitle: 'Select a content to embed',
    udwContentDiscoveredMethod: 'updateEmbed',
    label: 'Select antoher content item',
};
