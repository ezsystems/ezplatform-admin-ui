import React, { Component } from 'react';
import PropTypes from 'prop-types';
import EzButton from './ez-button';

export default class EzWidgetButton extends EzButton {
    /**
     * Returns the ezembed widget instance for the current selection.
     *
     * @method getWidget
     * @return CKEDITOR.plugins.widget
     */
    getWidget() {
        const editor = this.props.editor.get('nativeEditor');
        const wrapper = editor.getSelection().getStartElement();

        return editor.widgets.getByElement(wrapper);
    }
}
