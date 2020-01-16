(function(global, doc) {
    let filterTimeout;
    const SELECTOR_ITEM = '.ez-instant-filter__group-item';
    const timeout = 200;
    const filters = doc.querySelectorAll('.ez-instant-filter');
    const toggleGroupNameDisplay = (group) => {
        const hasVisibleChildren = !![...group.querySelectorAll(SELECTOR_ITEM)].filter((item) => !item.hasAttribute('hidden')).length;
        const groupName = group.querySelector('.ez-instant-filter__group-name');
        const methodName = hasVisibleChildren ? 'removeAttribute' : 'setAttribute';

        groupName[methodName]('hidden', true);
    };
    const filterItems = function(itemsMap, groups, event) {
        window.clearTimeout(filterTimeout);

        filterTimeout = window.setTimeout(() => {
            const query = event.target.value.toLowerCase();
            const results = itemsMap.filter((item) => item.label.includes(query));

            itemsMap.forEach((item) => item.element.setAttribute('hidden', true));
            results.forEach((item) => item.element.removeAttribute('hidden'));

            groups.forEach(toggleGroupNameDisplay);
        }, timeout);
    };
    const initFilter = (filter) => {
        const filterInput = filter.querySelector('.ez-instant-filter__input');
        const groups = [...filter.querySelectorAll('.ez-instant-filter__group')];
        const items = [...filter.querySelectorAll(SELECTOR_ITEM)];
        const itemsMap = items.reduce(
            (total, item) => [
                ...total,
                {
                    label: item.textContent.toLowerCase(),
                    element: item,
                },
            ],
            []
        );

        filterInput.addEventListener('change', filterItems.bind(filter, itemsMap, groups), false);
        filterInput.addEventListener('blur', filterItems.bind(filter, itemsMap, groups), false);
        filterInput.addEventListener('keyup', filterItems.bind(filter, itemsMap, groups), false);
    };

    filters.forEach(initFilter);
})(window, window.document);
