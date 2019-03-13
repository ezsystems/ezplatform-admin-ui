import EzConfigBase from './base';

let editorialInteractionListener = null;
let blurListener = null;

export default class EzConfgiFixedBase extends EzConfigBase {
    static getTopPosition(block, editor) {
        const toolbar = document.querySelector('.ae-toolbar-floating');
        const editorRect = editor.element.getClientRect();
        const toolbarHeight = toolbar.getBoundingClientRect().height;
        const offset = 10;
        const shouldBeFixed = editorRect.top - toolbarHeight - 2 * offset < 0;
        const top = shouldBeFixed ? offset : editorRect.top + editor.element.getWindow().getScrollPosition().y - toolbarHeight - offset;

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

        window.removeEventListener('scroll', this._updatePosition, false);
    }

    getArrowBoxClasses() {
        return 'ae-toolbar-floating ae-arrow-box ez-ae-arrow-box-left';
    }

    setPosition(payload) {
        const editor = payload.editor.get('nativeEditor');
        const block = EzConfgiFixedBase.getBlockElement(payload);
        const eventHandler = EzConfgiFixedBase.eventHandler.bind(this);

        window.removeEventListener('scroll', this._updatePosition, false);
        window.addEventListener('scroll', this._updatePosition, false);

        if (!editorialInteractionListener) {
            editorialInteractionListener = editor.on('editorInteraction', eventHandler);
        }

        if (!blurListener) {
            blurListener = editor.on('blur', eventHandler);
        }

        return EzConfgiFixedBase.setPositionFor.call(this, block, editor, EzConfgiFixedBase.getTopPosition.bind(this));
    }
}
