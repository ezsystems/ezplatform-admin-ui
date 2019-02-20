import React, { Component } from 'react';
import AlloyEditor from 'alloyeditor';

export default class EzBtnMoveDown extends Component {
    static get key() {
        return 'ezmovedown';
    }

    /**
     * Executes the eZMoveDown command.
     *
     * @method moveDown
     */
    moveDown() {
        const editor = this.props.editor.get('nativeEditor');

        editor.execCommand('eZMoveDown');
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const title = Translator.trans(/*@Desc("Move Down")*/ 'move_down_btn.title', {}, 'alloy_editor');

        return (
            <button
                className="ae-button ez-btn-ae ez-btn-ae--move-down"
                onClick={this.moveDown.bind(this)}
                tabIndex={this.props.tabIndex}
                title={title}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#circle-caret-down" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnMoveDown.key] = AlloyEditor.EzBtnMoveDown = EzBtnMoveDown;
eZ.addConfig('ezAlloyEditor.ezBtnMoveDown', EzBtnMoveDown);
