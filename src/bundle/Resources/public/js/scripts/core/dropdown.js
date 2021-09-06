(function(global, doc, eZ) {
    const CLASS_DROPDOWN_OVERFLOW = 'ibexa-dropdown--overflow';
    const CLASS_DROPDOWN_EXPANDED = 'ibexa-dropdown--is-expanded';
    const CLASS_ITEMS_POSITION_TOP = 'ibexa-dropdown__items--position-top';
    const CLASS_REMOVE_SELECTION = 'ibexa-dropdown__remove-selection';
    const CLASS_ITEM_SELECTED_IN_LIST = 'ibexa-dropdown__item--selected';
    const CLASS_ITEM_HIDDEN = 'ibexa-dropdown__item--hidden';
    const SELECTOR_ITEM = '.ibexa-dropdown__item';
    const SELECTOR_DROPDOWN = '.ibexa-dropdown';
    const SELECTOR_SOURCE = '.ibexa-dropdown__source .ibexa-input';
    const SELECTOR_ITEMS_CONTAINER = '.ibexa-dropdown__items';
    const SELECTOR_ITEMS_LIST_CONTAINER = '.ibexa-dropdown__items-list';
    const SELECTOR_ITEMS_FILTER = '.ibexa-dropdown__items-filter';
    const SELECTOR_SELECTED_ITEM_IN_LABEL = '.ibexa-dropdown__selected-item';
    const SELECTOR_SELECTED_ITEM_IN_LIST = '.ibexa-dropdown__item--selected';
    const SELECTOR_SELECTION_INFO = '.ibexa-dropdown__selection-info';
    const SELECTOR_OVERFLOW_ITEM_INFO = '.ibexa-dropdown__selected-overflow-number';
    const SELECTOR_PLACEHOLDER = '.ibexa-dropdown__selected-placeholder';
    const EVENT_VALUE_CHANGED = 'change';
    const ITEMS_LIST_MAX_HEIGHT = 300;
    const RESTRICTED_AREA_ITEMS_CONTAINER = 190;

    class Dropdown {
        constructor(config = {}) {
            const container = config.container ?? doc.querySelector(SELECTOR_DROPDOWN);
            const sourceInput = config.sourceInput ?? container.querySelector(SELECTOR_SOURCE);

            this.container = container;
            this.sourceInput = sourceInput;
            this.itemsContainer = config.itemsContainer ?? container.querySelector(SELECTOR_ITEMS_CONTAINER);
            this.itemsListContainer = config.itemsListContainer ?? container.querySelector(SELECTOR_ITEMS_LIST_CONTAINER);
            this.itemsFilterInput = config.itemsFilterInput ?? container.querySelector(SELECTOR_ITEMS_FILTER);
            this.hasDefaultSelection = config.hasDefaultSelection || false;
            this.selectedItemTemplate =
                config.selectedItemTemplate ||
                `<li
                class="ibexa-dropdown__selected-item"
                data-value="{{value}}">
                    {{label}}<span class="${CLASS_REMOVE_SELECTION}"></span>
            </li>`;
            this.canSelectOnlyOne = !sourceInput?.multiple;
            this.createSelectedItem = this.createSelectedItem.bind(this);
            this.selectFirstItem = this.selectFirstItem.bind(this);
            this.clearCurrentSelection = this.clearCurrentSelection.bind(this);
            this.hideOptions = this.hideOptions.bind(this);
            this.onSelect = this.onSelect.bind(this);
            this.onClickOutside = this.onClickOutside.bind(this);
            this.onInputClick = this.onInputClick.bind(this);
            this.onOptionClick = this.onOptionClick.bind(this);
            this.fireValueChangedEvent = this.fireValueChangedEvent.bind(this);
            this.filterItems = this.filterItems.bind(this);
            this.afterClose = this.afterClose.bind(this);
        }

        createSelectedItem(value, label) {
            return this.selectedItemTemplate.replace('{{value}}', value).replace('{{label}}', label);
        }

        selectFirstItem() {
            const items = this.itemsListContainer.querySelectorAll(SELECTOR_ITEM);
            const firstItem = items[0];
            const label = firstItem.querySelector('.ibexa-dropdown__item-label').innerHTML;

            items.forEach((item) => item.classList.remove(CLASS_ITEM_SELECTED_IN_LIST));
            firstItem.classList.add(CLASS_ITEM_SELECTED_IN_LIST);
            firstItem.querySelector('.ibexa-input').checked = true;

            this.container
                .querySelector(SELECTOR_SELECTION_INFO)
                .insertAdjacentHTML('beforeend', this.createSelectedItem(firstItem.dataset.value, label));
        }

        clearCurrentSelection() {
            const placeholder = this.container.querySelector(SELECTOR_PLACEHOLDER).cloneNode();
            const overflowNumber = this.container.querySelector(SELECTOR_OVERFLOW_ITEM_INFO).cloneNode();

            this.sourceInput.querySelectorAll('option').forEach((option) => (option.selected = false));
            this.itemsListContainer
                .querySelectorAll(SELECTOR_ITEM)
                .forEach((option) => option.classList.remove(CLASS_ITEM_SELECTED_IN_LIST));
            this.container.querySelector(SELECTOR_SELECTION_INFO).innerHTML = '';
            this.container.querySelector(SELECTOR_SELECTION_INFO).append(placeholder);
            this.container.querySelector(SELECTOR_SELECTION_INFO).append(overflowNumber);
        }

        hideOptions() {
            doc.body.removeEventListener('click', this.onClickOutside);

            return this.container.classList.remove(CLASS_DROPDOWN_EXPANDED);
        }

        onSelect(element, selected) {
            const value = element.dataset.value;

            if (this.canSelectOnlyOne && selected) {
                this.hideOptions();
                this.clearCurrentSelection();
            }

            if (value) {
                this.sourceInput.querySelector(`[value="${value}"]`).selected = selected;

                if (!this.canSelectOnlyOne) {
                    element.querySelector('.ibexa-input').checked = selected;
                }
            }

            this.itemsListContainer.querySelector(`[data-value="${value}"]`).classList.toggle(CLASS_ITEM_SELECTED_IN_LIST, selected);

            const selectedItemsList = this.container.querySelector(SELECTOR_SELECTION_INFO);

            if (selected && value) {
                const label = element.querySelector('.ibexa-dropdown__item-label').innerHTML;

                selectedItemsList
                    .querySelector(SELECTOR_PLACEHOLDER)
                    .insertAdjacentHTML('beforebegin', this.createSelectedItem(value, label));
            } else {
                const valueNode = selectedItemsList.querySelector(`[data-value="${value}"]`);

                if (valueNode) {
                    valueNode.remove();
                }
            }

            if (
                !selected &&
                this.hasDefaultSelection &&
                (this.canSelectOnlyOne || !selectedItemsList.querySelectorAll(SELECTOR_SELECTED_ITEM_IN_LABEL).length)
            ) {
                this.selectFirstItem();
            }

            this.fitItems();
            this.fireValueChangedEvent();
        }

        onClickOutside(event) {
            if (this.container.contains(event.target)) {
                return;
            }

            this.hideOptions();
            this.fireValueChangedEvent();
        }

        fireValueChangedEvent() {
            this.sourceInput.dispatchEvent(new CustomEvent(EVENT_VALUE_CHANGED));
        }

        getItemsContainerHeight(isItemsContainerAbove) {
            const DROPDOWN_MARGIN = 16;
            const SELECTOR_MODAL = '.modal[aria-modal=true]';
            const documentElementHeight = global.innerHeight;
            const itemsContainerTop = this.itemsContainer.getBoundingClientRect().top;

            if (isItemsContainerAbove) {
                return this.container.querySelector(SELECTOR_SELECTION_INFO).getBoundingClientRect().top - DROPDOWN_MARGIN;
            }

            if (this.itemsContainer.closest(SELECTOR_MODAL)) {
                return itemsContainerTop - DROPDOWN_MARGIN;
            }

            return documentElementHeight - itemsContainerTop - DROPDOWN_MARGIN;
        }

        onInputClick(event) {
            if (event.target.classList.contains(CLASS_REMOVE_SELECTION)) {
                this.deselectOption(event.target.closest(SELECTOR_SELECTED_ITEM_IN_LABEL));

                return;
            }

            const isDropdownExpanded = this.container.classList.toggle(CLASS_DROPDOWN_EXPANDED);
            const bodyMethodName = isDropdownExpanded ? 'addEventListener' : 'removeEventListener';

            if (isDropdownExpanded) {
                const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                const { top } = this.itemsContainer.getBoundingClientRect();
                const isItemsContainerAbove = top + ITEMS_LIST_MAX_HEIGHT > viewportHeight;

                this.itemsContainer.style['max-height'] = `${this.getItemsContainerHeight(isItemsContainerAbove)}px`;
                this.itemsContainer.classList.toggle(CLASS_ITEMS_POSITION_TOP, isItemsContainerAbove);
            }

            this.itemsFilterInput.focus();
            doc.body[bodyMethodName]('click', this.onClickOutside, false);
        }

        onOptionClick({ target }) {
            const option = target.closest(SELECTOR_ITEM);
            const isSelected = this.canSelectOnlyOne || !option.classList.contains(CLASS_ITEM_SELECTED_IN_LIST);

            return this.onSelect(option, isSelected);
        }

        deselectOption(option) {
            const value = option.dataset.value;
            const optionSelect = this.sourceInput.querySelector(`[value="${value}"]`);
            const itemSelected = this.itemsListContainer.querySelector(`[data-value="${value}"]`);

            itemSelected.classList.remove(CLASS_ITEM_SELECTED_IN_LIST);
            itemSelected.querySelector('.ibexa-input').checked = false;

            if (optionSelect) {
                optionSelect.selected = false;
            }

            option.remove();

            if (!this.itemsListContainer.querySelectorAll(SELECTOR_SELECTED_ITEM_IN_LIST).length && this.hasDefaultSelection) {
                this.hideOptions();
                this.clearCurrentSelection();
                this.selectFirstItem();
            }

            this.fitItems();
            this.fireValueChangedEvent();
        }

        fitItems() {
            if (this.canSelectOnlyOne) {
                return;
            }

            let itemsWidth = 0;
            let numberOfOverflowItems = 0;
            const selectedItemsContainer = this.container.querySelector(SELECTOR_SELECTION_INFO);
            const selectedItems = this.container.querySelectorAll(SELECTOR_SELECTED_ITEM_IN_LABEL);
            const selectedItemsOverflow = this.container.querySelector(SELECTOR_OVERFLOW_ITEM_INFO);

            if (selectedItemsOverflow) {
                selectedItems.forEach((item) => {
                    item.hidden = false;
                });
                selectedItems.forEach((item, index) => {
                    const isOverflowNumber = item.classList.contains('ibexa-dropdown__selected-overflow-number');

                    itemsWidth += item.offsetWidth;

                    if (
                        !isOverflowNumber &&
                        index !== 0 &&
                        itemsWidth > selectedItemsContainer.offsetWidth - RESTRICTED_AREA_ITEMS_CONTAINER
                    ) {
                        numberOfOverflowItems++;
                        item.hidden = true;
                    }
                });

                if (numberOfOverflowItems) {
                    selectedItemsOverflow.hidden = false;
                    selectedItemsOverflow.innerHTML = numberOfOverflowItems;
                    this.container.classList.add(CLASS_DROPDOWN_OVERFLOW);
                } else {
                    selectedItemsOverflow.hidden = true;
                    this.container.classList.remove(CLASS_DROPDOWN_OVERFLOW);
                }
            }
        }

        filterItems(event) {
            const allItems = this.itemsListContainer.querySelectorAll('[data-filter-value]');

            if (event.currentTarget.value.length < 3) {
                [...allItems].forEach((item) => item.classList.remove(CLASS_ITEM_HIDDEN));

                return;
            }

            const visibleItems = this.itemsListContainer.querySelectorAll(`[data-filter-value^="${event.currentTarget.value.toLowerCase()}"]`);

            [...allItems].forEach((item) => item.classList.add(CLASS_ITEM_HIDDEN));
            [...visibleItems].forEach((item) => item.classList.remove(CLASS_ITEM_HIDDEN));
        }

        afterClose(event) {
            if (event.propertyName === 'transform' && !this.container.classList.contains(CLASS_DROPDOWN_EXPANDED)) {
                this.itemsFilterInput
                    .closest('.ibexa-input-text-wrapper')
                    .querySelector('.ibexa-input-text-wrapper__action-btn--clear')
                    .click();
            }
        }

        init() {
            const isEmpty = !this.container.querySelectorAll(SELECTOR_SELECTED_ITEM_IN_LABEL).length;

            if (isEmpty && this.hasDefaultSelection) {
                this.selectFirstItem();
            }

            this.hideOptions();
            this.fitItems();

            this.container.querySelector(SELECTOR_SELECTION_INFO).onclick = this.onInputClick;
            this.itemsContainer.addEventListener('transitionend', this.afterClose, false);
            this.itemsListContainer
                .querySelectorAll(`${SELECTOR_ITEM}:not([disabled])`)
                .forEach((option) => (option.onclick = this.onOptionClick));
            this.itemsFilterInput.addEventListener('keyup', this.filterItems, false);
            this.itemsFilterInput.addEventListener('input', this.filterItems, false);
        }
    }

    eZ.addConfig('core.Dropdown', Dropdown);
})(window, window.document, window.eZ);
