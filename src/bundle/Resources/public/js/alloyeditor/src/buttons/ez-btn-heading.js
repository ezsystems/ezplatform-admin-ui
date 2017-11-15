import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzButton from '../base/ez-button';

export default class EzBtnHeading extends EzButton {
    static get key() {
        return 'ezheading';
    }

    /**
     * Executes the eZAppendContent to add a heading element in the editor.
     *
     * @method addHeading
     */
    addHeading() {
        this.execCommand({
            tagName: 'h1',
        });
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const css = 'ae-button ez-btn-ae ez-btn-ae--heading ' + this.getStateClasses();

        return (
            <button className={css} onClick={this.addHeading.bind(this)} tabIndex={this.props.tabIndex}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#h1"></use>
                </svg>
                <p className="ez-btn-ae__label">Heading</p>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnHeading.key] = AlloyEditor.EzBtnHeading = EzBtnHeading;

EzBtnHeading.propTypes = {
    command: PropTypes.string,
    modifiesSelection: PropTypes.bool,
};

EzBtnHeading.defaultProps = {
    command: 'eZAddContent',
    modifiesSelection: true,
};
