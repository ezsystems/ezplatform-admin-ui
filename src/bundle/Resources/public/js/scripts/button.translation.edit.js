(function(global, doc) {
    class EditTranslation {
        constructor(config) {
            this.container = config.container;
            this.toggler = config.container.querySelector('.ez-btn--translations-list-toggler');
            this.translationsList = config.container.querySelector('.ez-translation-selector__list-wrapper');
            this.listTriangle = config.container.querySelector('.ez-translation-selector__list-triangle');

            this.hideTranslationsList = this.hideTranslationsList.bind(this);
            this.showTranslationsList = this.showTranslationsList.bind(this);
            this.setPosition = this.setPosition.bind(this);
        }

        setPosition() {
            const togglerRect = this.toggler.getBoundingClientRect();
            const translationsListRect = this.translationsList.getBoundingClientRect();
            const topPosition = togglerRect.top + togglerRect.height / 2 - translationsListRect.height / 2;
            const leftPosition = togglerRect.left - translationsListRect.width - 10;

            if (topPosition + translationsListRect.height > window.innerHeight) {
                this.translationsList.style.bottom = 0;
                this.translationsList.style.top = 'auto';

                const translationsListRect = this.translationsList.getBoundingClientRect();
                const listTriangleTopPosition =
                    ((togglerRect.top + togglerRect.height / 2 - translationsListRect.top) / translationsListRect.height) * 100;

                this.listTriangle.style.top = listTriangleTopPosition < 90 ? `${listTriangleTopPosition}%` : '%';
            } else {
                this.translationsList.style.top = `${topPosition}px`;
                this.translationsList.style.bottom = 'auto';
                this.listTriangle.style.top = '50%';
            }

            this.translationsList.style.left = `${leftPosition}px`;

            this.translationsList.classList.remove('ez-translation-selector__list-wrapper--visually-hidden');
        }

        hideTranslationsList(event) {
            const closestTranslationSelector = event.target.closest('.ez-translation-selector');
            const clickedOnTranslationsList = closestTranslationSelector && closestTranslationSelector.isSameNode(this.container);
            const clickedOnDraftConflictModal = event.target.closest('.ez-modal--version-draft-conflict');

            if (clickedOnTranslationsList || clickedOnDraftConflictModal) {
                return;
            }

            this.translationsList.classList.add('ez-translation-selector__list-wrapper--hidden');
            this.translationsList.classList.add('ez-translation-selector__list-wrapper--visually-hidden');

            global.removeEventListener('scroll', this.setPosition, false);
            doc.removeEventListener('click', this.hideTranslationsList, false);
        }

        showTranslationsList() {
            this.translationsList.classList.remove('ez-translation-selector__list-wrapper--hidden');

            this.setPosition();

            global.addEventListener('scroll', this.setPosition, false);
            doc.addEventListener('click', this.hideTranslationsList, false);
        }

        init() {
            this.toggler.addEventListener('click', this.showTranslationsList, false);
        }
    }

    const translationSelectors = doc.querySelectorAll('.ez-translation-selector');

    if (!translationSelectors.length) {
        return;
    }

    translationSelectors.forEach((translationSelector) => {
        const editTranslation = new EditTranslation({ container: translationSelector });

        editTranslation.init();
    });
})(window, document);
