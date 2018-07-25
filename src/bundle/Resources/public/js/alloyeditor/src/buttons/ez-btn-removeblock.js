import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzButton from '../base/ez-button';

export default class EzBtnBlockRemove extends EzButton {
    static get key() {
        return 'ezblockremove';
    }

    /**
     * Executes the eZRemoveBlock to remove block.
     *
     * @method removeBlock
     * @protected
     */
    removeBlock(data) {
        this.execCommand(data);
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const title = Translator.trans(/*@Desc("Remove block")*/ 'remove_block_btn.title', {}, 'alloy_editor');

        return (
            <button
                className="ae-button ez-btn-ae ez-btn-ae--remove-block"
                onClick={this.removeBlock.bind(this)}
                tabIndex={this.props.tabIndex} title={title}
            >
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#trash"></use>
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnBlockRemove.key] = AlloyEditor.EzBtnBlockRemove = EzBtnBlockRemove;

EzBtnBlockRemove.propTypes = {
    command: PropTypes.string,
    modifiesSelection: PropTypes.bool,
};

EzBtnBlockRemove.defaultProps = {
    command: 'eZRemoveBlock',
    modifiesSelection: true,
};
