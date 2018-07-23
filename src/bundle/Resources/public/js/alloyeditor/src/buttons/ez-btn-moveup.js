import React, { Component } from 'react';
import AlloyEditor from 'alloyeditor';

export default class EzBtnMoveUp extends Component {
    static get key() {
        return 'ezmoveup';
    }

    /**
     * Executes the eZMoveUp command.
     *
     * @method moveUp
     */
    moveUp() {
        const editor = this.props.editor.get('nativeEditor');

        editor.execCommand('eZMoveUp');
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const title = Translator.trans(/*@Desc("Move Up")*/ 'move_up_btn.title', {}, 'alloy_editor');

        return (
            <button
                className="ae-button ez-btn-ae ez-btn-ae--move-up"
                onClick={this.moveUp.bind(this)}
                tabIndex={this.props.tabIndex}
                title={title}
            >
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#circle-caret-up" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnMoveUp.key] = AlloyEditor.EzBtnMoveUp = EzBtnMoveUp;
