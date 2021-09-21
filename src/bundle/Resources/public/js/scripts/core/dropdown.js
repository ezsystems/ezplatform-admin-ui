(function(global, doc, eZ) {
    const EVENT_VALUE_CHANGED = 'change';
    const ITEMS_LIST_MIN_HEIGHT = 300;
    const RESTRICTED_AREA_ITEMS_CONTAINER = 190;
    const MINIMUM_LETTERS_TO_FILTER = 3;

    class Dropdown {
        constructor(config = {}) {
            this.container = config.container.classList.contains('ibexa-dropdown') ? config.container : config.container.querySelector('.ibexa-dropdown');

            if (!this.container) {
                throw new Error('No valid container provided');
            }

            this.sourceInput = this.container.querySelector(config.selectorSource ?? '.ibexa-dropdown__source .ibexa-input');
            this.selectedItemsContainer = this.container.querySelector('.ibexa-dropdown__selection-info');
            this.itemsFixedWrapperContainer = this.container.querySelector('.ibexa-dropdown__items-fixed-wrapper');
            this.itemsContainer = this.container.querySelector('.ibexa-dropdown__items');
            this.itemsListContainer = this.itemsContainer.querySelector('.ibexa-dropdown__items-list');
            this.itemsFilterInput = this.itemsContainer.querySelector('.ibexa-dropdown__items-filter');

            this.hasDefaultSelection = config.hasDefaultSelection ?? false;
            this.canSelectOnlyOne = !this.sourceInput?.multiple;
            this.selectedItemTemplate = this.selectedItemsContainer.dataset.template;

            this.createSelectedItem = this.createSelectedItem.bind(this);
            this.selectFirstItem = this.selectFirstItem.bind(this);
            this.hideOptions = this.hideOptions.bind(this);
            this.fitItems = this.fitItems.bind(this);
            this.onInputClick = this.onInputClick.bind(this);
            this.clearCurrentSelection = this.clearCurrentSelection.bind(this);
            this.onSelect = this.onSelect.bind(this);
            this.onInteractionOutside = this.onInteractionOutside.bind(this);
            this.onOptionClick = this.onOptionClick.bind(this);
            this.fireValueChangedEvent = this.fireValueChangedEvent.bind(this);
            this.filterItems = this.filterItems.bind(this);
            this.removeExpandingClass = this.removeExpandingClass.bind(this);
        }

        getSelectedItems() {
            return this.selectedItemsContainer.querySelectorAll('.ibexa-dropdown__selected-item:not(.ibexa-dropdown__selected-item--predefined)');
        }

        createSelectedItem(value, label) {
            return this.selectedItemTemplate.replace('{{ value }}', value).replace('{{ label }}', label);
        }

        selectFirstItem() {
            const items = this.itemsListContainer.querySelectorAll('.ibexa-dropdown__item:not(.ibexa-dropdown__item--preferred-choice)');
            const [firstItem] = items;
            const label = firstItem.querySelector('.ibexa-dropdown__item-label').innerHTML;

            items.forEach((item) => item.classList.remove('ibexa-dropdown__item--selected'));
            firstItem.classList.add('ibexa-dropdown__item--selected');

            if (!this.canSelectOnlyOne) {
                firstItem.querySelector('.ibexa-input').checked = true;
            }

            this.selectedItemsContainer
                .querySelector('.ibexa-dropdown__selected-placeholder')
                .insertAdjacentHTML('beforebegin', this.createSelectedItem(firstItem.dataset.value, label));
        }

        clearCurrentSelection() {
            const placeholder = this.selectedItemsContainer.querySelector('.ibexa-dropdown__selected-placeholder').cloneNode();
            const overflowNumber = this.selectedItemsContainer.querySelector('.ibexa-dropdown__selected-overflow-number').cloneNode();

            this.sourceInput.querySelectorAll('option').forEach((option) => (option.selected = false));
            this.itemsListContainer
                .querySelectorAll('.ibexa-dropdown__item--selected')
                .forEach((option) => option.classList.remove('ibexa-dropdown__item--selected'));
            this.selectedItemsContainer.innerHTML = '';
            this.selectedItemsContainer.append(placeholder);
            this.selectedItemsContainer.append(overflowNumber);
        }

        hideOptions() {
            doc.body.removeEventListener('click', this.onClickOutside);

            return this.container.classList.remove('ibexa-dropdown--is-expanded');
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

            this.itemsListContainer.querySelector(`[data-value="${value}"]`).classList.toggle('ibexa-dropdown__item--selected', selected);

            const selectedItemsList = this.container.querySelector('.ibexa-dropdown__selection-info');

            if (selected && value) {
                const label = element.querySelector('.ibexa-dropdown__item-label').innerHTML;

                selectedItemsList
                    .querySelector('.ibexa-dropdown__selected-placeholder')
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
                (this.canSelectOnlyOne || !selectedItemsList.querySelectorAll('.ibexa-dropdown__selected-item').length)
            ) {
                this.selectFirstItem();
            }

            this.fitItems();
            this.fireValueChangedEvent();
        }

        onInteractionOutside(event) {
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
            const DROPDOWN_MARGIN = 32;
            const documentElementHeight = global.innerHeight;
            const { top, bottom } = this.selectedItemsContainer.getBoundingClientRect();

            if (isItemsContainerAbove) {
                return top - DROPDOWN_MARGIN;
            }

            return documentElementHeight - bottom - DROPDOWN_MARGIN;
        }

        removeExpandingClass(event) {
            if (event.propertyName === 'transform') {
                this.container.classList.remove('ibexa-dropdown--is-expanding');
                this.itemsContainer.removeEventListener('transitionend', this.removeExpandingClass, false);
            }
        }

        collapseDropdown() {
            this.itemsContainer.addEventListener('transitionend', this.removeExpandingClass, false);

            setTimeout(() => {
                this.container.classList.remove('ibexa-dropdown--is-expanded');
                this.hideOptions();
                this.fireValueChangedEvent();
            }, 1);
        }

        expandDropdown() {
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
            const { left, top, bottom, width } = this.selectedItemsContainer.getBoundingClientRect();
            const shouldItemsContainerBeAbove = bottom + ITEMS_LIST_MIN_HEIGHT > viewportHeight;

            this.itemsFixedWrapperContainer.style.left = `${left}px`;
            this.itemsFixedWrapperContainer.style.top = `${shouldItemsContainerBeAbove ? top : bottom}px`;

            this.itemsFixedWrapperContainer.classList
                .toggle('ibexa-dropdown__items-fixed-wrapper--position-top', shouldItemsContainerBeAbove);

            this.itemsContainer.style['max-height'] = `${this.getItemsContainerHeight(shouldItemsContainerBeAbove)}px`;
            this.itemsContainer.style.width = `${width}px`;

            this.container.classList.add('ibexa-dropdown--is-expanding');
            this.itemsFilterInput
                .closest('.ibexa-input-text-wrapper')
                .querySelector('.ibexa-input-text-wrapper__action-btn--clear')
                .click();
            this.itemsFilterInput.focus();

            setTimeout(() => {
                this.container.classList.add('ibexa-dropdown--is-expanded');
            }, 1);
        }

        onInputClick(event) {
            if (event.target.classList.contains('ibexa-dropdown__remove-selection')) {
                this.deselectOption(event.target.closest('.ibexa-dropdown__selected-item'));

                return;
            }

            const shouldExpandDropdown = !this.container.classList.contains('ibexa-dropdown--is-expanded');
            const bodyMethodName = shouldExpandDropdown ? 'addEventListener' : 'removeEventListener';

            if (shouldExpandDropdown) {
                this.expandDropdown();
            } else {
                this.collapseDropdown();
            }

            doc.body[bodyMethodName]('click', this.onInteractionOutside, false);
            doc[bodyMethodName]('scroll', this.onInteractionOutside, true);
        }

        onOptionClick({ target }) {
            const option = target.closest('.ibexa-dropdown__item');
            const isSelected = this.canSelectOnlyOne || !option.classList.contains('ibexa-dropdown__item--selected');

            return this.onSelect(option, isSelected);
        }

        deselectOption(option) {
            const value = option.dataset.value;
            const optionSelect = this.sourceInput.querySelector(`[value="${value}"]`);
            const itemSelected = this.itemsListContainer.querySelector(`[data-value="${value}"]`);
            const selectedItems = this.getSelectedItems();

            itemSelected.classList.remove('ibexa-dropdown__item--selected');

            if (!this.canSelectOnlyOne) {
                itemSelected.querySelector('.ibexa-input').checked = false;
            }

            if (optionSelect) {
                optionSelect.selected = false;
            }

            option.remove();

            if (!selectedItems.length && this.hasDefaultSelection) {
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
            const selectedItems = this.selectedItemsContainer.querySelectorAll('.ibexa-dropdown__selected-item');
            const selectedItemsOverflow = this.selectedItemsContainer.querySelector('.ibexa-dropdown__selected-overflow-number');

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
                        itemsWidth > this.selectedItemsContainer.offsetWidth - RESTRICTED_AREA_ITEMS_CONTAINER
                    ) {
                        numberOfOverflowItems++;
                        item.hidden = true;
                    }
                });

                if (numberOfOverflowItems) {
                    selectedItemsOverflow.hidden = false;
                    selectedItemsOverflow.innerHTML = numberOfOverflowItems;
                    this.container.classList.add('ibexa-dropdown--overflow');
                } else {
                    selectedItemsOverflow.hidden = true;
                    this.container.classList.remove('ibexa-dropdown--overflow');
                }
            }
        }

        compareItem(itemFilterValue, searchedTerm) {
            const itemFilterValueLowerCase = itemFilterValue.toLowerCase();
            const searchedTermLowerCase = searchedTerm.toLowerCase();

            return itemFilterValueLowerCase.indexOf(searchedTermLowerCase) === 0;
        }

        filterItems(event) {
            const forceShowItems = event.currentTarget.value.length < MINIMUM_LETTERS_TO_FILTER;
            const allItems = [...this.itemsListContainer.querySelectorAll('[data-filter-value]')];
            const separator = this.itemsListContainer.querySelector('.ibexa-dropdown__separator');
            let hideSeparator = true;

            if (separator) {
                separator.setAttribute('hidden', 'hidden');
            }

            allItems.forEach((item) => {
                const isItemVisible = forceShowItems || this.compareItem(item.dataset.filterValue, event.currentTarget.value);
                const isPreferredChoice = item.classList.contains('ibexa-dropdown__item--preferred-choice');

                if (isPreferredChoice && isItemVisible) {
                    hideSeparator = false;
                }

                item.classList.toggle('ibexa-dropdown__item--hidden', !isItemVisible);
            });

            if (separator && !hideSeparator) {
                separator.removeAttribute('hidden');
            }
        }

        init() {
            const selectedItems = this.getSelectedItems();
            const isEmpty = !selectedItems.length;

            if (isEmpty && this.hasDefaultSelection) {
                this.selectFirstItem();
            }

            this.hideOptions();
            this.fitItems();

            this.selectedItemsContainer.addEventListener('click', this.onInputClick, false);
            this.itemsListContainer
                .querySelectorAll('.ibexa-dropdown__item:not([disabled])')
                .forEach((option) => option.addEventListener('click', this.onOptionClick, false));
            this.itemsFilterInput.addEventListener('keyup', this.filterItems, false);
            this.itemsFilterInput.addEventListener('input', this.filterItems, false);
        }
    }

    eZ.addConfig('core.Dropdown', Dropdown);
})(window, window.document, window.eZ);
