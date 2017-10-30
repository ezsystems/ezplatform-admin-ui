import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzEmbedImageButton from '../base/ez-embedimage';

export default class EzBtnImageVariation extends EzEmbedImageButton {
    static get key() {
        return 'ezimagevariation';
    }

    /**
     * Change event handler. It updates the image in the editor so that the
     * newly choosen variation is used.
     *
     * @method updateImage
     * @protected
     * @param {Object} event
     */
    updateImage(event) {
        const widget = this.getWidget();
        const newVariation = event.target.value;

        widget.setConfig('size', newVariation).setWidgetContent('');
        widget.focus();
        widget.loadImageVariation(widget.variations[newVariation].href);
    }

    /**
     * Returns the options to add to the drop down list.
     *
     * @method getImageVariationOptions
     * @return Array
     */
    getImageVariationOptions() {
        return Object.keys(eZ.adminUiConfig.imageVariations).map(variation => (<option key={variation} value={variation}>{variation}</option>));
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        return (
            <select
                defaultValue={this.getWidget().getConfig('size')}
                onChange={this.updateImage.bind(this)}
                tabIndex={this.props.tabIndex}
            >
                {this.getImageVariationOptions()}
            </select>
        );
    }
}

AlloyEditor.Buttons[EzBtnImageVariation.key] = AlloyEditor.EzBtnImageVariation = EzBtnImageVariation;
