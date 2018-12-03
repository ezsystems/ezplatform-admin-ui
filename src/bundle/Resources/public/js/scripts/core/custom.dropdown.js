(function(global, doc) {
    const CLASS_CUSTOM_DROPDOWN = 'ez-custom-dropdown';
    const CLASS_CUSTOM_DROPDOWN_ITEM = 'ez-custom-dropdown__item';
    const CLASS_ITEMS_HIDDEN = 'ez-custom-dropdown__items--hidden';
    const CLASS_REMOVE_SELECTION = 'ez-custom-dropdown__remove-selection';
    const CLASS_ITEM_SELECTED_IN_LIST = 'ez-custom-dropdown__item--selected';
    const SELECTOR_ITEM = '.ez-custom-dropdown__item';
    const SELECTOR_SELECTED_ITEM_IN_LABEL = '.ez-custom-dropdown__selected-item';
    const SELECTOR_SELECTED_ITEM_IN_LIST = '.ez-custom-dropdown__item--selected';
    const SELECTOR_SELECTION_INFO = '.ez-custom-dropdown__selection-info';
    const SELECTOR_PLACEHOLDER = '[data-value=""]';
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
            this.fireValueChangedEvent = this.fireValueChangedEvent.bind(this);
        }

        createSelectedItem(value, label) {
            return this.selectedItemTemplate.replace('{{value}}', value).replace('{{label}}', label);
        }

        selectFirstItem() {
            const items = this.itemsContainer.querySelectorAll(`${SELECTOR_ITEM}`);
            const firstItem = items[0];

            items.forEach((item) => item.classList.remove(CLASS_ITEM_SELECTED_IN_LIST));
            firstItem.classList.add(CLASS_ITEM_SELECTED_IN_LIST);

            this.container
                .querySelector(SELECTOR_SELECTION_INFO)
                .insertAdjacentHTML('beforeend', this.createSelectedItem(firstItem.dataset.value, firstItem.innerHTML));
        }

        clearCurrentSelection() {
            this.sourceInput.querySelectorAll('option').forEach((option) => (option.selected = false));
            this.itemsContainer.querySelectorAll(SELECTOR_ITEM).forEach((option) => option.classList.remove(CLASS_ITEM_SELECTED_IN_LIST));
            this.container.querySelector(SELECTOR_SELECTION_INFO).innerHTML = '';
        }

        hideOptions() {
            doc.body.removeEventListener('click', this.onClickOutside);

            return this.itemsContainer.classList.add(CLASS_ITEMS_HIDDEN);
        }

        onSelect(element, selected) {
            const value = element.dataset.value;
            const cssMethodName = selected ? 'add' : 'remove';

            if (this.canSelectOnlyOne && selected) {
                this.hideOptions();
                this.clearCurrentSelection();
            }

            if (value) {
                this.sourceInput.querySelector(`[value="${value}"]`).selected = selected;
            }

            this.itemsContainer.querySelector(`[data-value="${value}"]`).classList[cssMethodName](CLASS_ITEM_SELECTED_IN_LIST);

            const selectedItemsList = this.container.querySelector(SELECTOR_SELECTION_INFO);

            if (selected && value) {
                const placeholder = selectedItemsList.querySelector(SELECTOR_PLACEHOLDER);

                if (placeholder) {
                    placeholder.remove();

                    this.itemsContainer.querySelector(SELECTOR_PLACEHOLDER).classList.remove(CLASS_ITEM_SELECTED_IN_LIST);
                }

                selectedItemsList.insertAdjacentHTML('beforeend', this.createSelectedItem(value, element.innerHTML));
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

        onInputClick(event) {
            if (event.target.classList.contains(CLASS_REMOVE_SELECTION)) {
                this.deselectOption(event.target.closest(SELECTOR_SELECTED_ITEM_IN_LABEL));

                return;
            }

            const methodName = this.itemsContainer.classList.contains(CLASS_ITEMS_HIDDEN) ? 'addEventListener' : 'removeEventListener';

            this.itemsContainer.classList.toggle(CLASS_ITEMS_HIDDEN);
            doc.body[methodName]('click', this.onClickOutside, false);
        }

        onOptionClick({ target }) {
            const option = target.classList.contains(CLASS_CUSTOM_DROPDOWN_ITEM) ? target : target.closest(SELECTOR_ITEM);

            return this.onSelect(option, !option.classList.contains(CLASS_ITEM_SELECTED_IN_LIST));
        }

        deselectOption(option) {
            const value = option.dataset.value;
            const optionSelect = this.sourceInput.querySelector(`[value="${value}"]`);

            this.itemsContainer.querySelector(`[data-value="${value}"]`).classList.remove(CLASS_ITEM_SELECTED_IN_LIST);

            if (optionSelect) {
                optionSelect.selected = false;
            }

            option.remove();

            if (!this.itemsContainer.querySelectorAll(SELECTOR_SELECTED_ITEM_IN_LIST).length && this.hasDefaultSelection) {
                this.hideOptions();
                this.clearCurrentSelection();
                this.selectFirstItem();
            }

            this.fireValueChangedEvent();
        }

        init() {
            const isEmpty = !this.container.querySelectorAll(SELECTOR_SELECTED_ITEM_IN_LABEL).length;

            this.container.classList.add(CLASS_CUSTOM_DROPDOWN);

            if (isEmpty && this.hasDefaultSelection) {
                this.selectFirstItem();
            }

            this.hideOptions();

            this.container.querySelector(SELECTOR_SELECTION_INFO).onclick = this.onInputClick;
            this.itemsContainer
                .querySelectorAll(`${SELECTOR_ITEM}:not([disabled])`)
                .forEach((option) => (option.onclick = this.onOptionClick));
        }
    }

    global.eZ.addConfig('core.CustomDropdown', CustomDropdown);
})(window, window.document);
