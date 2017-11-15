import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzEmbedAlign from '../base/ez-embedalign';

export default class EzEmbedAlignCenter extends EzEmbedAlign {
    static get key() {
        return 'ezembedcenter';
    }
}

AlloyEditor.Buttons[EzEmbedAlignCenter.key] = AlloyEditor.EzEmbedAlignCenter = EzEmbedAlignCenter;

EzEmbedAlignCenter.defaultProps = {
    alignment: 'center',
    iconName: 'image-center',
    cssClassSuffix: 'embed-center',
    label: 'Center',
};
