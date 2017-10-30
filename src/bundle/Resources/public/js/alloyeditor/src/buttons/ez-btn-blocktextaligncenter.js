import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzBlockTextAlign from '../base/ez-blocktextalign';

export default class EzBtnBlockTextAlignCenter extends EzBlockTextAlign {
    static get key() {
        return 'ezblocktextaligncenter';
    }
}

AlloyEditor.Buttons[EzBtnBlockTextAlignCenter.key] = AlloyEditor.EzBtnBlockTextAlignCenter = EzBtnBlockTextAlignCenter;

EzBtnBlockTextAlignCenter.defaultProps = {
    textAlign: 'center',
    iconName: 'align-center',
    cssClassSuffix: 'align-center',
    label: 'Center',
};
