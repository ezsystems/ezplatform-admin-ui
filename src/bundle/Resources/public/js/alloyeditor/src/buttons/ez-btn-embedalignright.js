import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzEmbedAlign from '../base/ez-embedalign';

export default class EzEmbedAlignRight extends EzEmbedAlign {
    static get key() {
        return 'ezembedright';
    }
}

AlloyEditor.Buttons[EzEmbedAlignRight.key] = AlloyEditor.EzEmbedAlignRight = EzEmbedAlignRight;

EzEmbedAlignRight.defaultProps = {
    alignment: 'right',
    iconName: 'image-right',
    cssClassSuffix: 'embed-right',
    label: Translator.trans(/*@Desc("Right")*/ 'embed_align_right_btn.label', {}, 'alloy_editor'),
};
