import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';

const CLASS_HAS_ANCHOR = 'ez-has-anchor';
const CLASS_ICON_ANCHOR = 'ez-icon--anchor';

export default class EzBtnAnchorEdit extends Component {
    constructor(props) {
        super(props);

        this.updateValue = this.updateValue.bind(this);
        this.saveAnchor = this.saveAnchor.bind(this);
        this.removeAnchor = this.removeAnchor.bind(this);
        this.fireCustomUpdateEvent = this.fireCustomUpdateEvent.bind(this);

        this.getStateClasses = AlloyEditor.ButtonStateClasses.getStateClasses;

        this.block = null;

        this.state = {
            value: '',
        };
    }

    componentDidMount() {
        const block = this.findBlock();
        const value = block.getId();

        this.setState(() => ({ value }));
    }

    static get key() {
        return 'ezanchoredit';
    }

    findBlock() {
        const nativeEditor = this.props.editor.get('nativeEditor');
        const focused = nativeEditor.widgets.focused;
        const path = nativeEditor.elementPath();
        let block = path.block;

        if (this.block) {
            return this.block;
        }

        if (!block && focused) {
            block = focused.element;
        }

        if (block && block.is('li')) {
            block = block.getParent();
        }

        if (!block && path.contains('table')) {
            block = path.elements.find((element) => element.is('table'));
        }

        this.block = block;

        return block;
    }

    findIcon() {
        const block = this.findBlock();

        return [...block.getChildren().$].find((child) => child.classList && child.classList.contains(CLASS_ICON_ANCHOR));
    }

    updateValue({ nativeEvent }) {
        const value = nativeEvent.target.value;

        this.setState(() => ({ value }));
    }

    fireCustomUpdateEvent() {
        const nativeEditor = this.props.editor.get('nativeEditor');

        nativeEditor.fire('customUpdate');
    }

    removeAnchor() {
        const block = this.findBlock();
        const icon = this.findIcon();

        block.removeAttribute('id');
        block.removeClass(CLASS_HAS_ANCHOR);

        if (icon) {
            icon.remove();
        }

        this.props.cancelExclusive();

        block.focus();

        this.fireCustomUpdateEvent();
    }

    saveAnchor() {
        const { value } = this.state;
        const block = this.findBlock();
        const icon = this.findIcon();

        block.setAttribute('id', value);
        block.addClass(CLASS_HAS_ANCHOR);

        if (!icon) {
            this.renderIcon();
        }

        this.props.cancelExclusive();

        block.focus();

        this.fireCustomUpdateEvent();
    }

    renderIcon() {
        const block = this.findBlock();
        const icon = `
            <svg class="ez-icon ez-icon--small ez-icon--secondary ${CLASS_ICON_ANCHOR}">
                <use xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#link-anchor"></use>
            </svg>`;

        block.$.insertAdjacentHTML('afterbegin', icon);
    }

    /**
     * Lifecycle. Renders the UI of the button.
     *
     * @method render
     * @return {Object} The content which should be rendered.
     */
    render() {
        const nameLabel = Translator.trans(/*@Desc("Name:")*/ 'anchor_edit.input.label', {}, 'alloy_editor');
        const removeBtnTitle = Translator.trans(/*@Desc("Remove")*/ 'anchor_edit.btn.remove.title', {}, 'alloy_editor');
        const saveBtnTitle = Translator.trans(/*@Desc("Save")*/ 'anchor_edit.btn.save.title', {}, 'alloy_editor');
        const { value } = this.state;
        const isDisabled = !value;

        return (
            <div className="ez-ae-anchor-edit">
                <div className="ez-ae-anchor-edit__input-wrapper">
                    <label className="ez-ae-anchor-edit__input-label">{nameLabel}</label>
                    <input type="text" className="ez-ae-anchor-edit__input form-control" onChange={this.updateValue} value={value} />
                </div>
                <div className="ez-ae-anchor-edit__actions">
                    <button
                        type="button"
                        title={removeBtnTitle}
                        className="ez-ae-anchor-edit__btn ez-ae-anchor-edit__btn--trash"
                        onClick={this.removeAnchor}
                        disabled={isDisabled}>
                        <svg className="ez-icon ez-icon--light ez-icon--medium ez-btn-ae__icon">
                            <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#trash" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        title={saveBtnTitle}
                        className="ez-ae-anchor-edit__btn ez-ae-anchor-edit__btn--save"
                        onClick={this.saveAnchor}
                        disabled={isDisabled}>
                        <svg className="ez-icon ez-icon--light ez-icon--medium ez-btn-ae__icon">
                            <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#checkmark" />
                        </svg>
                    </button>
                </div>
            </div>
        );
    }
}

AlloyEditor.Buttons[EzBtnAnchorEdit.key] = AlloyEditor.EzBtnAnchorEdit = EzBtnAnchorEdit;
eZ.addConfig('ezAlloyEditor.ezBtnAnchorEdit', EzBtnAnchorEdit);
