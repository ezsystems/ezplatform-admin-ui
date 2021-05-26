(function(global, doc, eZ) {
    const CLASS_POPUP_MENU_HIDDEN = 'ibexa-popup-menu--hidden';
    class PopupMenu {
        constructor(config) {
            this.popupMenuElement = config.popupMenuElement;
            this.triggerElement = config.triggerElement;
            this.onItemClick = config.onItemClick;
            this.position = config.position || (() => {});
            
            this.handleToggle = this.handleToggle.bind(this);
            this.handleClickOutsidePopupMenu = this.handleClickOutsidePopupMenu.bind(this);

            doc.addEventListener('click', this.handleToggle, false);
            doc.addEventListener('click', this.handleClickOutsidePopupMenu, false);
        }

        generateItems(itemsToGenerate, processAfterCreated) {
            const { itemTemplate } = this.popupMenuElement.dataset;
            const fragment = doc.createDocumentFragment();

            itemsToGenerate.forEach((item) => {
                const container = doc.createElement('ul');
                const renderedItem = itemTemplate.replace('{{ label }}', item.label);

                container.insertAdjacentHTML('beforeend', renderedItem);

                const popupMenuItem = container.querySelector('li');

                processAfterCreated(popupMenuItem, item);

                popupMenuItem.addEventListener('click', (event) => {
                    this.popupMenuElement.classList.add(CLASS_POPUP_MENU_HIDDEN);
                    this.onItemClick(event);
                });

                fragment.append(popupMenuItem);
            });

            this.popupMenuElement.innerHTML = '';
            this.popupMenuElement.append(fragment);
        }

        getItems() {
            return this.popupMenuElement.querySelectorAll('.ibexa-popup-menu__item');
        }

        toggleItems(shouldHide) {
            const popupMenuItems = [...this.popupMenuElement.querySelectorAll('.ibexa-popup-menu__item')];

            popupMenuItems.forEach((popupMenuItem) => {
                popupMenuItem.classList.toggle('ibexa-popup-menu__item--hidden', shouldHide(popupMenuItem));
            });
        }

        handleToggle() {
            this.popupMenuElement.classList.toggle(CLASS_POPUP_MENU_HIDDEN);
            this.adapt();
        }

        handleClickOutsidePopupMenu(event) {
            const isPopupMenuExpanded = !this.popupMenuElement.classList.contains(CLASS_POPUP_MENU_HIDDEN);
            const isClickInsideParentElement = this.triggerElement.contains(event.target);

            if (!isPopupMenuExpanded || isClickInsideParentElement) {
                return;
            }

            this.popupMenuElement.classList.add(CLASS_POPUP_MENU_HIDDEN);
        }

        adapt() {
            const isHidden = this.popupMenuElement.classList.contains(CLASS_POPUP_MENU_HIDDEN);

            if (isHidden) {
                return;
            }

            this.position(this.popupMenuElement);
        }
    }

    eZ.addConfig('core.PopupMenu', PopupMenu);
})(window, window.document, window.eZ);
