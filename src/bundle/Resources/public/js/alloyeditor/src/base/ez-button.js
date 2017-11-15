import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzButton extends Component {
    constructor(props) {
        super(props);

        this.getStateClasses = AlloyEditor.ButtonStateClasses.getStateClasses;
        this.execCommand = AlloyEditor.ButtonCommand.execCommand.bind(this);
    }
}
