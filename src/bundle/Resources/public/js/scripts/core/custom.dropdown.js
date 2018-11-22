(function(global, doc) {
    const CLASS_CUSTOM_DROPDOWN = 'ez-custom-dropdown';
    const CLASS_ITEMS_HIDDEN = 'ez-custom-dropdown__items--hidden';
    const CLASS_REMOVE_SELECTION = 'ez-custom-dropdown__remove-selection';
    const CLASS_ITEM_SELECTED = 'ez-custom-dropdown__item--selected';
    const SELECTOR_ITEMS = '.ez-custom-dropdown__items';
    const SELECTOR_ITEM = '.ez-custom-dropdown__item';
    const SELECTOR_SELECTED_ITEM = '.ez-custom-dropdown__selected-item';
    const SELECTOR_SELECTION_INFO = '.ez-custom-dropdown__selection-info';
    const EVENT_VALUE_CHANGED = 'valueChanged';

    class CustomDropdown {
        constructor(config) {
            this.container = config.container;
            this.sourceInput = config.sourceInput;
            this.itemsContainer = config.itemsContainer;
            this.hasDefaultSelection = config.hasDefaultSelection || false;
            this.selectedItemTemplate =
                config.selectedItemTemplate ||
                `<li
                class="ez-custom-dropdown__selected-item"
                data-value="{{value}}">
                    {{label}}<span class="${CLASS_REMOVE_SELECTION}"></span>
            </li>`;

            this.canSelectOnlyOne = !config.sourceInput.multiple;
            this.createSelectedItem = this.createSelectedItem.bind(this);
            this.selectFirstItem = this.selectFirstItem.bind(this);
            this.clearCurrentSelection = this.clearCurrentSelection.bind(this);
            this.hideOptions = this.hideOptions.bind(this);
            this.onSelect = this.onSelect.bind(this);
            this.onClickOutside = this.onClickOutside.bind(this);
            this.onInputClick = this.onInputClick.bind(this);
            this.onOptionClick = this.onOptionClick.bind(this);
        }

        createSelectedItem(value, label) {
            return this.selectedItemTemplate.replace('{{value}}', value).replace('{{label}}', label);
        }

        selectFirstItem() {
            const items = this.itemsContainer.querySelectorAll(`${SELECTOR_ITEM}`);
            const firstItem = items[0];

            items.forEach((item) => item.classList.remove(CLASS_ITEM_SELECTED));
            firstItem.classList.add(CLASS_ITEM_SELECTED);

            this.container
                .querySelector(SELECTOR_SELECTION_INFO)
                .insertAdjacentHTML('beforeend', this.createSelectedItem(firstItem.dataset.value, firstItem.innerHTML));
        }

        clearCurrentSelection() {
            this.sourceInput.querySelectorAll('option').forEach((option) => (option.selected = false));
            this.itemsContainer.querySelectorAll(SELECTOR_ITEM).forEach((option) => option.classList.remove(CLASS_ITEM_SELECTED));
            this.container.querySelector(SELECTOR_SELECTION_INFO).innerHTML = '';
        }

        hideOptions() {
            return this.itemsContainer.classList.add(CLASS_ITEMS_HIDDEN);
        }

        removePlaceholderOption() {
            this.itemsContainer.querySelector('[data-value=""]').selected = false;
        }

        onSelect(element, selected) {
            const value = element.dataset.value;
            const cssMethodName = selected ? 'add' : 'remove';

            if (this.canSelectOnlyOne && selected) {
                this.hideOptions();
                this.clearCurrentSelection();
            }

            this.removePlaceholderOption();

            if (value) {
                this.sourceInput.querySelector(`[value="${value}"]`).selected = selected;
            }

            this.itemsContainer.querySelector(`[data-value="${value}"]`).classList[cssMethodName](CLASS_ITEM_SELECTED);

            if (selected && value) {
                this.container
                    .querySelector(SELECTOR_SELECTION_INFO)
                    .insertAdjacentHTML('beforeend', this.createSelectedItem(value, element.innerHTML));
            } else {
                const valueNode = this.container.querySelector(`${SELECTOR_SELECTION_INFO} [data-value="${value}"]`);

                if (valueNode) {
                    valueNode.remove();
                }
            }

            if (this.canSelectOnlyOne && !selected && this.hasDefaultSelection) {
                this.hideOptions();
                this.selectFirstItem();
            }

            this.sourceInput.dispatchEvent(new CustomEvent(EVENT_VALUE_CHANGED));
        }

        onClickOutside(event) {
            if (event.target.closest(SELECTOR_SELECTION_INFO) || event.target.closest(SELECTOR_ITEMS)) {
                return;
            }

            this.hideOptions();
            this.sourceInput.dispatchEvent(new CustomEvent(EVENT_VALUE_CHANGED));
        }

        onInputClick(event) {
            if (event.target.classList.contains(CLASS_REMOVE_SELECTION)) {
                this.onSelect(event.target.closest(SELECTOR_SELECTED_ITEM), false);

                return;
            }

            const methodName = this.itemsContainer.classList.contains(CLASS_ITEMS_HIDDEN) ? 'addEventListener' : 'removeEventListener';

            this.itemsContainer.classList.toggle(CLASS_ITEMS_HIDDEN);
            doc.querySelector('body')[methodName]('click', this.onClickOutside, false);
        }

        onOptionClick(event) {
            return this.onSelect(event.target, !event.target.classList.contains(CLASS_ITEM_SELECTED));
        }

        init() {
            const isEmpty = !this.container.querySelectorAll(SELECTOR_SELECTED_ITEM).length;

            this.container.classList.add(CLASS_CUSTOM_DROPDOWN);

            if (isEmpty && this.hasDefaultSelection) {
                this.selectFirstItem();
            }

            this.container.querySelector(SELECTOR_SELECTION_INFO).onclick = this.onInputClick;
            this.itemsContainer
                .querySelectorAll(`${SELECTOR_ITEM}:not([disabled])`)
                .forEach((option) => (option.onclick = this.onOptionClick));
        }
    }

    global.eZ.addConfig('core.CustomDropdown', CustomDropdown);
})(window, window.document);
