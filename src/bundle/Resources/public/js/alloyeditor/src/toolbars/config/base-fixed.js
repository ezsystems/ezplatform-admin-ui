import EzConfigBase from './base';

const TOOLBAR_OFFSET = 10;
let isScrollEventAdded = false;
let originalComponentWillUnmount = null;

export default class EzConfgiFixedBase extends EzConfigBase {
    static getTopPosition(block, editor) {
        const toolbar = document.querySelector('.ae-toolbar-floating');
        const editorRect = editor.element.getClientRect();
        const toolbarHeight = toolbar ? toolbar.getBoundingClientRect().height : 0;
        const shouldBeFixed = editorRect.top - toolbarHeight - 2 * TOOLBAR_OFFSET < 0;
        const header = document.querySelector('.ez-edit-header__content-type-name');
        const top = shouldBeFixed
            ? TOOLBAR_OFFSET + (header ? header.offsetHeight : 0)
            : editorRect.top + editor.element.getWindow().getScrollPosition().y - toolbarHeight - TOOLBAR_OFFSET;

        if (toolbar) {
            toolbar.classList.toggle('ae-toolbar-floating--fixed', shouldBeFixed);
        }

        return top;
    }

    static componentWillUnmount() {
        if (typeof originalComponentWillUnmount === 'function') {
            originalComponentWillUnmount();
        }

        isScrollEventAdded = false;

        window.removeEventListener('scroll', this._updatePosition, false);
    }

    getArrowBoxClasses() {
        return 'ae-toolbar-floating ae-arrow-box ez-ae-arrow-box-left';
    }

    setPosition(payload) {
        const editor = payload.editor.get('nativeEditor');
        const block = EzConfgiFixedBase.getBlockElement(payload);

        if (!isScrollEventAdded) {
            originalComponentWillUnmount = this.componentWillUnmount.bind(this);
            this.componentWillUnmount = EzConfgiFixedBase.componentWillUnmount.bind(this);

            isScrollEventAdded = true;

            window.addEventListener('scroll', this._updatePosition, false);
        }

        return EzConfgiFixedBase.setPositionFor.call(this, block, editor, EzConfgiFixedBase.getTopPosition.bind(this));
    }
}
