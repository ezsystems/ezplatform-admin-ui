import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import EzWidgetButton from './ez-widgetbutton';

export default class EzEmbedDiscoverContentButton extends EzWidgetButton {
    constructor(props) {
        super(props);

        this.confirmHandler = this.confirmHandler.bind(this);
    }

    confirmHandler() {
        const { editor, udwContentDiscoveredMethod } = this.props;

        editor.get('nativeEditor').unlockSelection(true);

        this[udwContentDiscoveredMethod].apply(this, arguments);
    }

    cancelHandler(udwContainer) {
        this.props.editor.get('nativeEditor').unlockSelection(true);

        ReactDOM.unmountComponentAtNode(udwContainer);
    }

    /**
     * Triggers the UDW to choose the content to embed.
     *
     * @method chooseContent
     */
    chooseContent() {
        const { udwIsSelectableMethod, udwConfigName, udwTitle, editor } = this.props;
        const selectable = udwIsSelectableMethod ? this[udwIsSelectableMethod] : (item, callback) => callback(true);
        const udwContainer = document.querySelector('#react-udw');
        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
        const languageCode = document.querySelector('meta[name="LanguageCode"]').content;
        const config = JSON.parse(document.querySelector(`[data-udw-config-name="${udwConfigName}"]`).dataset.udwConfig);
        const alloyEditorCallbacks = eZ.ezAlloyEditor.callbacks;
        const mergedConfig = Object.assign(
            {
                onConfirm: this.confirmHandler,
                onCancel: this.cancelHandler.bind(this, udwContainer),
                title: udwTitle,
                multiple: false,
                startingLocationId: window.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
                restInfo: { token, siteaccess },
                canSelectContent: selectable,
                cotfAllowedLanguages: [languageCode],
            },
            config
        );

        editor.get('nativeEditor').lockSelection();

        if (alloyEditorCallbacks && typeof alloyEditorCallbacks.openUdw === 'function') {
            alloyEditorCallbacks.openUdw(mergedConfig);
        } else {
            ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, mergedConfig), udwContainer);
        }
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
