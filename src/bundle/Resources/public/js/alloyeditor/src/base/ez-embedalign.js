import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import WidgetButton from './ez-widgetbutton';

export default class EzEmbedAlign extends WidgetButton {
    /**
     * Checks if the configured alignment is active on the focused embed
     * element.
     *
     * @method isActive
     * @return {Boolean}
     */
    isActive() {
        return this.getWidget().isAligned(this.props.alignment);
    }

    /**
     * Applies or un-applies the alignment on the currently focused embed
     * element.
     *
     * @method applyStyle
     */
    applyStyle() {
        const widget = this.getWidget();

        if (this.isActive()) {
            widget.unsetAlignment();
        } else {
            widget.setAlignment(this.props.alignment);
        }
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const cssClass = 'ae-button ez-btn-ae ez-btn-ae--' + this.props.cssClassSuffix + ' ' + this.getStateClasses();
        const icon = '/bundles/ezplatformadminui/img/ez-icons.svg#' + this.props.iconName;

        return (
            <button className={cssClass} onClick={this.applyStyle.bind(this)}
                tabIndex={this.props.tabIndex} title={this.props.label}>
                <svg className='ez-icon ez-btn-ae__icon'>
                    <use xlinkHref={icon}></use>
                </svg>
            </button>
        );
    }
}

EzEmbedAlign.propTypes = {
    editor: PropTypes.object.isRequired,
    label: PropTypes.string.isRequired,
    tabIndex: PropTypes.number.isRequired,
    alignment: PropTypes.string.isRequired,
    iconName: PropTypes.string.isRequired,
    cssClassSuffix: PropTypes.string.isRequired,
};
