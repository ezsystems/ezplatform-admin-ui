import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBtnTable extends Component {
    static get key() {
        return 'eztable';
    }

    /**
     * Checks if the command is disabled in the current selection.
     *
     * @method isDisabled
     * @return {Boolean} True if the command is disabled, false otherwise.
     */
    isDisabled() {
        const editor = this.props.editor.get('nativeEditor');
        const predecessors = editor.elementPath().elements;
        const restrictedPredecessors = ['li'];

        let isDisabled = false;

        predecessors.forEach((predecessor) => {
            const predecessorName = predecessor.getName();

            if (restrictedPredecessors.includes(predecessorName)) {
                isDisabled = true;

                return;
            }
        });

        return isDisabled;
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        if (this.props.renderExclusive) {
            return <AlloyEditor.ButtonTableEdit {...this.props} />;
        }

        const label = Translator.trans(/*@Desc("Table")*/ 'table_btn.label', {}, 'alloy_editor');
        const css = 'ae-button ez-btn-ae ez-btn-ae--table';
        const disabled = this.isDisabled();

        return (
            <button
                className={css}
                disabled={disabled}
                onClick={this.props.requestExclusive}
                tabIndex={this.props.tabIndex}
                title={label}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#table-add" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[EzBtnTable.key] = AlloyEditor.EzBtnTable = EzBtnTable;
eZ.addConfig('ezAlloyEditor.ezBtnTable', EzBtnTable);

EzBtnTable.propTypes = {
    editor: PropTypes.object.isRequired,
    label: PropTypes.string.isRequired,
    tabIndex: PropTypes.number.isRequired,
};
