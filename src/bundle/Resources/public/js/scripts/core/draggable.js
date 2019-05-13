(function(global, doc, eZ) {
    const SELECTOR_PLACEHOLDER = '.ez-draggable__placeholder';
    const TIMEOUT_REMOVE_PLACEHOLDERS = 500;
    const DEFAULTS = {
        afterInit: () => {},
        afterDragStart: () => {},
        afterDragOver: () => {},
        afterDrop: () => {},
        attachCustomEventHandlersToItem: () => {},
    };

    const attachEventHandlersToItem = Symbol('attachEventHandlersToItem');
    const onDragStart = Symbol('onDragStart');
    const onDragOver = Symbol('onDragOver');
    const onDrop = Symbol('onDrop');
    const addPlaceholder = Symbol('addPlaceholder');
    const removePlaceholder = Symbol('removePlaceholder');
    const removePlaceholderAfterTimeout = Symbol('removePlaceholderAfterTimeout');
    const draggedItem = Symbol('draggedItem');
    const placeholder = Symbol('placeholder');
    const onDragOverTimeout = Symbol('onDragOverTimeout');

    class Draggable {
        constructor(config) {
            this[draggedItem] = null;
            this[placeholder] = null;
            this[onDragOverTimeout] = null;
            this.itemsContainer = config.itemsContainer;
            this.selectorItem = config.selectorItem;
            this.selectorPlaceholder = config.selectorPlaceholder || SELECTOR_PLACEHOLDER;
            this.afterInit = config.afterInit || DEFAULTS.afterInit;
            this.afterDragStart = config.afterDragStart || DEFAULTS.afterDragStart;
            this.afterDragOver = config.afterDragOver || DEFAULTS.afterDragOver;
            this.afterDrop = config.afterDrop || DEFAULTS.afterDrop;
            this.attachCustomEventHandlersToItem = config.attachCustomEventHandlersToItem || DEFAULTS.attachCustomEventHandlersToItem;

            this[onDragStart] = this[onDragStart].bind(this);
            this[onDragOver] = this[onDragOver].bind(this);
            this[onDrop] = this[onDrop].bind(this);
            this[addPlaceholder] = this[addPlaceholder].bind(this);
            this[removePlaceholder] = this[removePlaceholder].bind(this);
            this[removePlaceholderAfterTimeout] = this[removePlaceholderAfterTimeout].bind(this);
            this[attachEventHandlersToItem] = this[attachEventHandlersToItem].bind(this);
        }

        [attachEventHandlersToItem](item) {
            item.ondragstart = this[onDragStart];
            item.ondrag = this[removePlaceholderAfterTimeout];

            this.attachCustomEventHandlersToItem(item);
        }

        [onDragStart](event) {
            event.dataTransfer.dropEffect = 'move';
            event.dataTransfer.setData('text/html', event.currentTarget);

            this[draggedItem] = event.currentTarget;
            this.afterDragStart(this[draggedItem]);
        }

        [onDragOver](event) {
            const item = event.target.closest(`${this.selectorItem}:not(${this.selectorPlaceholder})`);

            if (!item) {
                return false;
            }

            this[removePlaceholder]();
            this[addPlaceholder](item, event.clientY);
            this.afterDragOver();
        }

        [onDrop]() {
            this.itemsContainer.insertBefore(this[draggedItem], this.itemsContainer.querySelector(this.selectorPlaceholder));
            this[removePlaceholder]();
            this.afterDrop(this[draggedItem]);
        }

        [addPlaceholder](element, positionY) {
            const container = doc.createElement('div');
            const rect = element.getBoundingClientRect();
            const middlePositionY = rect.top + rect.height / 2;
            const where = positionY <= middlePositionY ? element : element.nextSibling;

            container.insertAdjacentHTML('beforeend', this.itemsContainer.dataset.placeholder);

            this[placeholder] = container.querySelector(this.selectorPlaceholder);

            this.itemsContainer.insertBefore(this[placeholder], where);
            this[removePlaceholderAfterTimeout]();
        }

        [removePlaceholder]() {
            if (this[placeholder]) {
                this[placeholder].remove();
            }
        }

        [removePlaceholderAfterTimeout]() {
            global.clearTimeout(this[onDragOverTimeout]);

            this[onDragOverTimeout] = global.setTimeout(() => this[removePlaceholder](), TIMEOUT_REMOVE_PLACEHOLDERS);
        }

        init() {
            this.itemsContainer.ondragover = this[onDragOver];
            this.itemsContainer.addEventListener('drop', this[onDrop], false);

            this.itemsContainer.querySelectorAll(this.selectorItem).forEach(this[attachEventHandlersToItem]);

            this.afterInit();
        }

        reinit() {
            this.itemsContainer.removeEventListener('drop', this[onDrop]);

            this.init();
        }
    }

    eZ.addConfig('core.Draggable', Draggable);
})(window, window.document, window.eZ);
