import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzEmbedImageButton from '../base/ez-embedimage';

export default class EzBtnImageUpdate extends EzEmbedImageButton {
    static get key() {
        return 'ezimageupdate';
    }

    /**
     * Updates the image element with the selected content in UDW.
     *
     * @method updateImage
     * @param {Array} items the result of the choice in the UDW
     * @protected
     */
    updateImage(items) {
        const contentId = items[0].ContentInfo.Content._id;
        const content = items[0].ContentInfo.Content;
        const widget = this.getWidget();

        this.setContentInfo(contentId);
        widget.focus();
        widget.setWidgetContent("");
        widget.loadImagePreviewFromCurrentVersion(content.CurrentVersion._href, content.Name);

        ReactDOM.unmountComponentAtNode(document.querySelector('#react-udw'));
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const css = 'ae-button ez-btn-ae ez-btn-ae--imageupdate ' + this.getStateClasses();

        return (
            <button className={css} onClick={this.chooseContent.bind(this)} tabIndex={this.props.tabIndex}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#image"></use>
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnImageUpdate.key] = AlloyEditor.EzBtnImageUpdate = EzBtnImageUpdate;

EzBtnImageUpdate.defaultProps = {
    udwTitle: 'Select an image to embed',
    udwContentDiscoveredMethod: 'updateImage',
    udwConfigName: 'richtext_embed_image',
    label: 'Select antoher image item',
};
