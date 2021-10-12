(function(global, doc, eZ) {
    class TagViewSelect {
        constructor(config) {
            this.inputSelector = config.inputSelector || 'input';
            this.fieldContainer = config.fieldContainer;

            if (!this.fieldContainer) {
                throw new Error('Field Container doesn\'t exist!');
            }

            this.container = this.fieldContainer.querySelector('.ibexa-tag-view-select');
            this.listContainer = this.container.querySelector('.ibexa-tag-view-select__selected-list');
            this.inputField = this.fieldContainer.querySelector(this.inputSelector);
            this.selectBtn = this.container.querySelector('.ibexa-tag-view-select__btn-select-path')
            this.isSingleSelect = this.container.dataset.isSingleSelect === '1';
            this.canBeEmpty = this.container.dataset.canBeEmpty === '1';
            this.inputSeparator = config.seperator || ',';
            this.selectedItemTemplate = this.listContainer.dataset.template;

            this.addItems = this.addItems.bind(this);
            this.addItem = this.addItem.bind(this);
            this.removeItems = this.removeItems.bind(this);
            this.removeItem = this.removeItem.bind(this);
            this.toggleDeleteButtons = this.toggleDeleteButtons.bind(this);
            this.attachDeleteEvents = this.attachDeleteEvents.bind(this);
            this.adjustButtonLabel = this.adjustButtonLabel.bind(this);

            this.attachDeleteEvents();

            this.disabledObserver = new MutationObserver((mutationsList) => {
                const isDisabled = mutationsList[0].target.hasAttribute('disabled');

                this.toggleDisabledState(isDisabled);
            });

            this.disabledObserver.observe(this.container, {
                attributeFilter: ['disabled'],
                attributeOldValue: true,
            });
        }

        toggleDisabledState(isDisabled) {
            const removeBtns = this.listContainer.querySelectorAll('.ibexa-tag-view-select__selected-item-tag-remove-btn');

            removeBtns.forEach((btn) => btn.toggleAttribute('disabled', isDisabled));
            this.inputField.toggleAttribute('disabled', isDisabled);
            this.selectBtn.toggleAttribute('disabled', isDisabled);

        }

        addItems(items, forceRecreate) {
            if (this.isSingleSelect) {
                this.inputField.value = items[0]?.id ?? '';
                this.listContainer.textContent = '';
            } else {
                const newItemsIds = items.map((item) => item.id);

                if (this.inputField.value !== '' && !forceRecreate) {
                    newItemsIds.unshift(this.inputField.value);
                }

                this.inputField.value = newItemsIds.join(this.inputSeparator);
            }

            if (forceRecreate) {
                this.listContainer.textContent = '';
            }

            items.forEach((item) => {
                const { id, name } = item;
                const itemTemplate = this.selectedItemTemplate.replace('{{ id }}', id).replace('{{ name }}', name);
                const range = doc.createRange();
                const itemHtmlWidget = range.createContextualFragment(itemTemplate);
                const deleteButton = itemHtmlWidget.querySelector('.ibexa-tag-view-select__selected-item-tag-remove-btn');

                deleteButton.toggleAttribute('disabled', false);
                deleteButton.addEventListener('click', () => this.removeItem(id), false);
                this.listContainer.append(itemHtmlWidget);
            })

            this.inputField.dispatchEvent(new Event('change'));
            this.toggleDeleteButtons();
            this.adjustButtonLabel();
        }

        addItem(id, name, forceRecreate) {
            this.addItems([{id, name}], forceRecreate);
        }

        removeItems(items) {
            const prevSelectedIds = this.inputField.value.split(this.inputSeparator);
            const nextSelectedIds = prevSelectedIds.filter((savedId) => !items.includes(parseInt(savedId, 10)));
            this.inputField.value = nextSelectedIds.join(this.inputSeparator);

            items.forEach((itemId) => {
                this.listContainer.querySelector(`[data-id="${itemId}"]`).remove();
            });

            this.inputField.dispatchEvent(new Event('change'));
            this.toggleDeleteButtons();
            this.adjustButtonLabel();
        }

        removeItem(id) {
            this.removeItems([id]);
        }

        toggleDeleteButtons() {
            const selectedItems = [...this.listContainer.querySelectorAll('[data-id]')];
            const hideDeleteButtons = !this.canBeEmpty && selectedItems.length === 1;

            selectedItems.forEach((selectedItem) => selectedItem.querySelector('.ibexa-tag-view-select__selected-item-tag-remove-btn').toggleAttribute('hidden', hideDeleteButtons));
        }

        attachDeleteEvents() {
            const selectedItems = [...this.listContainer.querySelectorAll('[data-id]')];

            selectedItems.forEach((selectedItem) => {
                const id = parseInt(selectedItem.dataset.id, 10);
                const deleteButton = selectedItem.querySelector('.ibexa-tag-view-select__selected-item-tag-remove-btn');

                deleteButton.addEventListener('click', () => this.removeItem(id), false)
            });
        }

        adjustButtonLabel() {
            const selectedItems = [...this.listContainer.querySelectorAll('[data-id]')];
            const buttonLabelSelect = this.container.querySelector('.ibexa-tag-view-select__btn-label--select');
            const buttonLabelChange = this.container.querySelector('.ibexa-tag-view-select__btn-label--change');
            const hasButtonChangeLabel = this.isSingleSelect && selectedItems.length > 0;

            buttonLabelSelect.toggleAttribute('hidden', hasButtonChangeLabel);
            buttonLabelChange.toggleAttribute('hidden', !hasButtonChangeLabel);
        }
    }

    eZ.addConfig('core.TagViewSelect', TagViewSelect);
})(window, window.document, window.eZ);
