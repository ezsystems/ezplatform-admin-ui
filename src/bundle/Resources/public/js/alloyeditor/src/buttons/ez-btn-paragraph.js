import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzButton from '../base/ez-button';

export default class EzBtnParagraph extends EzButton {
    static get key() {
        return 'ezparagraph';
    }

    /**
     * Executes the eZAppendContent to add a paragraph element in the editor.
     *
     * @method addParagraph
     */
    addParagraph() {
        this.execCommand({
            tagName: 'p',
            content: '<br>',
        });
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

AlloyEditor.Buttons[EzBtnParagraph.key] = AlloyEditor.EzBtnParagraph = EzBtnParagraph;
eZ.addConfig('ezAlloyEditor.ezBtnParagraph', EzBtnParagraph);

EzBtnParagraph.propTypes = {
    command: PropTypes.string,
    modifiesSelection: PropTypes.bool,
};

EzBtnParagraph.defaultProps = {
    command: 'eZAddContent',
    modifiesSelection: true,
};
