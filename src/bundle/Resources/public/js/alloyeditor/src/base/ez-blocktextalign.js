import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

export default class EzBlockTextAlign extends Component {
    constructor(props) {
        super(props);

        this.getStateClasses = AlloyEditor.ButtonStateClasses.getStateClasses;
    }

    /**
     * Finds active block.
     *
     * @method findBlock
     * @return {Object}
     */
    findBlock() {
        return this.props.editor.get('nativeEditor').elementPath().block;
    }

    /**
     * Checks whether the element holding the caret already has the current
     * text align style
     *
     * @method isActive
     * @return {Boolean}
     */
    isActive() {
        return this.findBlock().getStyle('textAlign') === this.props.textAlign;
    }

    /**
     * Applies or removes the text align style
     *
     * @method applyStyle
     */
    applyStyle() {
        const block = this.findBlock();
        const editor = this.props.editor.get('nativeEditor');

        if (this.isActive()) {
            block.removeStyle('text-align');
        } else {
            block.setStyle('text-align', this.props.textAlign);
        }

        editor.fire('actionPerformed', this);
        editor.fire('customUpdate');
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const cssClass = 'ae-button ez-btn-ae ez-btn-ae--' + this.props.cssClassSuffix + ' ' + this.getStateClasses();
        const icon = '/bundles/ezplatformadminui/img/ez-icons.svg#' + this.props.iconName;

        return (
            <button className={cssClass} onClick={this.applyStyle.bind(this)} tabIndex={this.props.tabIndex} title={this.props.label}>
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref={icon} />
                </svg>
            </button>
        );
    }
}

EzBlockTextAlign.propTypes = {
    editor: PropTypes.object.isRequired,
    label: PropTypes.string.isRequired,
    tabIndex: PropTypes.number.isRequired,
    textAlign: PropTypes.string.isRequired,
    iconName: PropTypes.string.isRequired,
    cssClassSuffix: PropTypes.string.isRequired,
};
