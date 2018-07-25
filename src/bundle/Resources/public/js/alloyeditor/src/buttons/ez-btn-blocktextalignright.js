import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzBlockTextAlign from '../base/ez-blocktextalign';

export default class EzBtnBlockTextAlignRight extends EzBlockTextAlign {
    static get key() {
        return 'ezblocktextalignright';
    }
}

AlloyEditor.Buttons[EzBtnBlockTextAlignRight.key] = AlloyEditor.EzBtnBlockTextAlignRight = EzBtnBlockTextAlignRight;

EzBtnBlockTextAlignRight.defaultProps = {
    textAlign: 'right',
    iconName: 'align-right',
    cssClassSuffix: 'align-right',
    label: Translator.trans(/*@Desc("Right")*/ 'blocktext_align_right_btn.label', {}, 'alloy_editor'),
};
