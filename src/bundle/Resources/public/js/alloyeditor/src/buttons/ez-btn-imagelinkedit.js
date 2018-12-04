import React, { Component } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzBtnLinkEdit from './ez-btn-linkedit';

export default class EzBtnImageLinkEdit extends EzBtnLinkEdit {
    constructor(props) {
        super(props);

        this.widget = this.getWidget();
    }

    static get key() {
        return 'ezimagelinkedit';
    }

    componentWillUnmount() {
        if (!this.state.discoveringContent && this.state.isTemporary) {
            this.removeLink();
        }

        this.widget.removeLinkEditState();
        this.props.cancelExclusive();
    }

    getInitialState() {
        const widget = this.getWidget();
        const linkHref = widget.getEzLinkAttribute('href');
        const linkTarget = widget.getEzLinkAttribute('target');
        const linkTitle = widget.getEzLinkAttribute('title');
        const isTemporary = widget.getEzLinkAttribute('data-ez-temporary-link');

        return {
            linkHref: linkHref || '',
            linkTarget: linkTarget || '',
            linkTitle: linkTitle || '',
            isTemporary: isTemporary || false,
        };
    }

    getWidget() {
        const editor = this.props.editor.get('nativeEditor');
        const wrapper = editor.getSelection().getStartElement();

        return editor.widgets.getByElement(wrapper);
    }

    udwOnConfirm(udwContainer, items) {
        this.widget.setEzLinkAttribute('href', 'ezlocation://' + items[0].id);
        this.widget.setLinkEditState();
        this.widget.setFocused(true);

        ReactDOM.unmountComponentAtNode(udwContainer);
    }

    removeLink() {
        const link = this.widget.getEzLinkElement();

        link.remove();

        this.widget.removeLinkEditState();
        this.widget.removeIsLinkedState();
        this.widget.setFocused(true);

        this.props.cancelExclusive();
    }

    updateLink() {
        const { linkHref, linkTarget, linkTitle } = this.state;
        const hrefMethodName = linkHref === '' ? 'removeEzLinkAttribute' : 'setEzLinkAttribute';
        const targetMethodName = linkTarget === '' ? 'removeEzLinkAttribute' : 'setEzLinkAttribute';
        const titleMethodName = linkTitle === '' ? 'removeEzLinkAttribute' : 'setEzLinkAttribute';

        this.widget[hrefMethodName]('href', linkHref);
        this.widget[hrefMethodName]('data-cke-saved-href', linkHref);
        this.widget[targetMethodName]('target', linkTarget);
        this.widget[titleMethodName]('title', linkTitle);

        this.widget.removeEzLinkAttribute('data-ez-temporary-link');
        this.widget.removeLinkEditState();
        this.widget.setIsLinkedState();

        this.widget.setFocused(true);

        this.props.cancelExclusive();
    }
}

AlloyEditor.Buttons[EzBtnImageLinkEdit.key] = AlloyEditor.EzBtnImageLinkEdit = EzBtnImageLinkEdit;
