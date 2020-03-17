import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzButton from '../base/ez-button';

export default class EzBtnParagraphTableCell extends EzButton {
    static get key() {
        return 'ezparagraph-tablecell';
    }

    addParagraph() {
        this.props.editor.get('nativeEditor').insertHtml('<p></p>');
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const label = Translator.trans(/*@Desc("Paragraph")*/ 'paragraph_btn.label', {}, 'alloy_editor');
        const css = 'ae-button ez-btn-ae ez-btn-ae--paragraph ' + this.getStateClasses();

        return (
            <button className={css} onClick={this.addParagraph.bind(this)} tabIndex={this.props.tabIndex} title={label}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#paragraph-add" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnParagraphTableCell.key] = AlloyEditor.EzBtnParagraphTableCell = EzBtnParagraphTableCell;
eZ.addConfig('ezAlloyEditor.EzBtnParagraphTableCell', EzBtnParagraphTableCell);