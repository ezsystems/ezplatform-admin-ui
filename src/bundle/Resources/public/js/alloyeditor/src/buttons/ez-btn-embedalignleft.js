import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzEmbedAlign from '../base/ez-embedalign';

export default class EzEmbedAlignLeft extends EzEmbedAlign {
    static get key() {
        return 'ezembedleft';
    }
}

AlloyEditor.Buttons[EzEmbedAlignLeft.key] = AlloyEditor.EzEmbedAlignLeft = EzEmbedAlignLeft;

EzEmbedAlignLeft.defaultProps = {
    alignment: 'left',
    iconName: 'image-left',
    cssClassSuffix: 'embed-left',
    label: 'Left',
};
