import EzConfigBase from './base';

const TOOLBAR_OFFSET = 10;
let editorialInteractionListener = null;
let blurListener = null;
let isScrollEventAdded = false;

export default class EzConfgiFixedBase extends EzConfigBase {
    static getTopPosition(block, editor) {
        const toolbar = document.querySelector('.ae-toolbar-floating');
        const editorRect = editor.element.getClientRect();
        const toolbarHeight = toolbar.getBoundingClientRect().height;
        const shouldBeFixed = editorRect.top - toolbarHeight - 2 * TOOLBAR_OFFSET < 0;
        const top = shouldBeFixed
            ? TOOLBAR_OFFSET
            : editorRect.top + editor.element.getWindow().getScrollPosition().y - toolbarHeight - TOOLBAR_OFFSET;

        toolbar.classList.toggle('ae-toolbar-floating--fixed', shouldBeFixed);

        return top;
    }

    static eventHandler() {
        if (document.querySelector('.ae-toolbar-floating')) {
            return;
        }

        editorialInteractionListener.removeListener();
        blurListener.removeListener();

        editorialInteractionListener = null;
        blurListener = null;
        isScrollEventAdded = false;

        window.removeEventListener('scroll', this._updatePosition, false);
    }

    getArrowBoxClasses() {
        return 'ae-toolbar-floating ae-arrow-box ez-ae-arrow-box-left';
    }

    setPosition(payload) {
        const editor = payload.editor.get('nativeEditor');
        const block = EzConfgiFixedBase.getBlockElement(payload);
        const eventHandler = EzConfgiFixedBase.eventHandler.bind(this);

        if (!isScrollEventAdded) {
            window.addEventListener('scroll', this._updatePosition, false);

            isScrollEventAdded = true;
        }

        if (!editorialInteractionListener) {
            editorialInteractionListener = editor.on('editorInteraction', eventHandler);
        }

        if (!blurListener) {
            blurListener = editor.on('blur', eventHandler);
        }

        return EzConfgiFixedBase.setPositionFor.call(this, block, editor, EzConfgiFixedBase.getTopPosition.bind(this));
    }
}
