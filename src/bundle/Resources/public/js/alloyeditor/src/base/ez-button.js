import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzButton extends Component {
    constructor(props) {
        super(props);

        this.getStateClasses = AlloyEditor.ButtonStateClasses.getStateClasses;
        this.execCommand = AlloyEditor.ButtonCommand.execCommand.bind(this);
    }

    findSelectedBlock() {
        const nativeEditor = this.props.editor.get('nativeEditor');
        const path = nativeEditor.elementPath();
        let block = path.lastElement;

        if (block.hasClass('cke_widget_wrapper')) {
            block = nativeEditor.widgets.getByElement(block).element;
        }

        if (this.block) {
            return this.block;
        }

        this.block = block;

        return block;
    }
}
