(function (global, doc) {
    const SELECTOR_TEMPLATE = '.ezselection-settings-option-value-prototype';
    const SELECTOR_OPTION = '.ezselection-settings-option-value';
    const SELECTOR_OPTIONS_LIST = '.ezselection-settings-option-list';
    const SELECTOR_BTN_REMOVE = '.ezselection-settings-option-remove';
    const SELECTOR_BTN_ADD = '.ezselection-settings-option-add';
    const NUMBER_PLACEHOLDER = /__number__/g;

    [...doc.querySelectorAll('.ezselection-settings.options')].forEach(container => {
        const countExistingOptions = () => container.querySelectorAll(SELECTOR_OPTION).length;
        const findCheckedOptions = () => [...container.querySelectorAll('.ezselection-settings-option-checkbox:checked')];
        const toggleDisableState = () => {
            const disabledState = !!findCheckedOptions().length;
            const methodName = disabledState ? 'removeAttribute' : 'setAttribute';

            container.querySelector(SELECTOR_BTN_REMOVE)[methodName]('disabled', disabledState);
        };
        const getNextId = () => {
            if (countExistingOptions() === 0) {
                return 0;
            }
            const lastInput = container.querySelector(SELECTOR_OPTIONS_LIST).lastElementChild.querySelector('input[type="text"]');
            const lastId = lastInput.dataset.ezselectionOptionId;

            return lastId + 1;
        };
        const addOption = () => {
            const template = container.querySelector(SELECTOR_TEMPLATE).innerHTML;

            container
                .querySelector(SELECTOR_OPTIONS_LIST)
                .insertAdjacentHTML('beforeend', template.replace(NUMBER_PLACEHOLDER, getNextId()));
        };
        const removeOptions = () => {
            findCheckedOptions().forEach(element => element.closest(SELECTOR_OPTION).remove());
            toggleDisableState();
        };

        container.querySelector(SELECTOR_OPTIONS_LIST).addEventListener('click', toggleDisableState, false);
        container.querySelector(SELECTOR_BTN_ADD).addEventListener('click', addOption, false);
        container.querySelector(SELECTOR_BTN_REMOVE).addEventListener('click', removeOptions, false);
    });
})(window, document);
