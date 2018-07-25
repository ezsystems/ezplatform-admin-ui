import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzBlockTextAlign from '../base/ez-blocktextalign';

export default class EzBtnBlockTextAlignLeft extends EzBlockTextAlign {
    static get key() {
        return 'ezblocktextalignleft';
    }
}

AlloyEditor.Buttons[EzBtnBlockTextAlignLeft.key] = AlloyEditor.EzBtnBlockTextAlignLeft = EzBtnBlockTextAlignLeft;

EzBtnBlockTextAlignLeft.defaultProps = {
    textAlign: 'left',
    iconName: 'align-left',
    cssClassSuffix: 'align-left',
    label: Translator.trans(/*@Desc("Left")*/ 'blocktext_align_left_btn.label', {}, 'alloy_editor'),
};
