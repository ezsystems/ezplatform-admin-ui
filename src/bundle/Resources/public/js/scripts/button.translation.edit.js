(function(global, doc) {
    const togglerButtons = doc.querySelectorAll('.ez-btn--translations-list-toggler');
    let setPositionCallback = null;
    let hideTranslationsListCallback = null;
    const hideTranslationsList = (translationsList, event) => {
        const clickedOnTranslationsList = event.target.closest('.ez-translation-selector');
        const clickedOnDraftConflictModal = event.target.closest('.ez-modal--version-draft-conflict');

        if (clickedOnTranslationsList || clickedOnDraftConflictModal) {
            return;
        }

        translationsList.classList.add('ez-translation-selector__list-wrapper--hidden');
        translationsList.classList.add('ez-translation-selector__list-wrapper--visually-hidden');

        global.removeEventListener('scroll', setPositionCallback, false);
        doc.removeEventListener('click', hideTranslationsListCallback, false);
    };
    const showTranslationsList = (event) => {
        const translationSelector = event.currentTarget.closest('.ez-translation-selector');
        const translationsList = translationSelector.querySelector('.ez-translation-selector__list-wrapper');

        translationsList.classList.remove('ez-translation-selector__list-wrapper--hidden');

        setPosition(translationsList, event.currentTarget);

        setPositionCallback = setPosition.bind(this, translationsList, event.currentTarget);
        hideTranslationsListCallback = hideTranslationsList.bind(this, translationsList);

        global.addEventListener('scroll', setPositionCallback, false);
        doc.addEventListener('click', hideTranslationsListCallback, false);
    };
    const setPosition = (translationsList, button) => {
        const buttonRect = button.getBoundingClientRect();
        const translationsListRect = translationsList.getBoundingClientRect();
        const topPosition = buttonRect.top + buttonRect.height / 2 - translationsListRect.height / 2;
        const leftPosition = buttonRect.left - translationsListRect.width - 10;

        translationsList.style.top = topPosition + 'px';
        translationsList.style.left = leftPosition + 'px';

        translationsList.classList.remove('ez-translation-selector__list-wrapper--visually-hidden');
    };

    if (!togglerButtons.length) {
        return;
    }

    togglerButtons.forEach((button) => button.addEventListener('click', showTranslationsList, false));
})(window, document);
