import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import EzWidgetButton from './ez-widgetbutton';

export default class EzEmbedDiscoverContentButton extends EzWidgetButton {
    /**
     * Triggers the UDW to choose the content to embed.
     *
     * @method chooseContent
     */
    chooseContent() {
        const selectable = this.props.udwIsSelectableMethod ? this[this.props.udwIsSelectableMethod] : (item, callback) => callback(true);
        const udwContainer = document.querySelector('#react-udw');
        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;

        ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, {
            onConfirm: this[this.props.udwContentDiscoveredMethod].bind(this),
            onCancel: () => udwContainer.innerHTML = '',
            confirmLabel: 'Select content',
            title: this.props.udwTitle,
            multiple: false,
            startingLocationId: window.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
            restInfo: {token, siteaccess},
            canSelectContent: selectable,
        }), udwContainer);
    }

    /**
     * Sets the href of the ezembed widget based on the given content info
     *
     * @method setContentInfo
     * @param {eZ.ContentInfo} contentInfo
     */
    setContentInfo(contentId) {
        const embedWidget = this.getWidget();

        embedWidget.setHref('ezcontent://' + contentId);
        embedWidget.focus();
    }
}
