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
        widget.setWidgetContent('');
        widget.loadImagePreviewFromCurrentVersion(content.CurrentVersion._href, content.Name);

        ReactDOM.unmountComponentAtNode(document.querySelector('#react-udw'));
        this.fireCustomUpdateEvent();
    }

    /**
     * Fires a custom event to reflect changes in the RichText field.
     *
     * @method fireCustomUpdateEvent
     */
    fireCustomUpdateEvent() {
        const nativeEditor = this.props.editor.get('nativeEditor');

        nativeEditor.fire('customUpdate');
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
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#image" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnImageUpdate.key] = AlloyEditor.EzBtnImageUpdate = EzBtnImageUpdate;
eZ.addConfig('ezAlloyEditor.ezBtnImageUpdate', EzBtnImageUpdate);

EzBtnImageUpdate.defaultProps = {
    udwTitle: Translator.trans(/*@Desc("Select an image to embed")*/ 'image_update_btn.udw.title', {}, 'alloy_editor'),
    udwContentDiscoveredMethod: 'updateImage',
    udwConfigName: 'richtext_embed_image',
    label: Translator.trans(/*@Desc("Select another image item")*/ 'image_update_btn.label', {}, 'alloy_editor'),
};
