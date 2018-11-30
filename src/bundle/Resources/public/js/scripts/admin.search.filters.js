(function(global, doc, $) {
    let getUsersTimeout;
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const filterBtn = doc.querySelector('.ez-btn--filter');
    const filters = doc.querySelector('.ez-filters');
    const clearBtn = filters.querySelector('.ez-btn-clear');
    const applyBtn = filters.querySelector('.ez-btn-apply');
    const selectBtns = [...doc.querySelectorAll('.ez-btn--select')];
    const dateFields = [...doc.querySelectorAll('.ez-date-select')];
    const contentTypeSelector = doc.querySelector('.ez-content-type-selector');
    const contentTypeSelect = doc.querySelector('.ez-filters__item--content-type .ez-filters__select');
    const sectionSelect = doc.querySelector('.ez-filters__item--section .ez-filters__select');
    const lastModifiedSelect = doc.querySelector('.ez-filters__item--modified .ez-filters__select');
    const lastCreatedSelect = doc.querySelector('.ez-filters__item--created .ez-filters__select');
    const creatorInput = doc.querySelector('.ez-filters__item--creator .ez-filters__input');
    const searchCreatorInput = doc.querySelector('#search_creator');
    const usersList = doc.querySelector('.ez-filters__item--creator .ez-filters__user-list');
    const resetCreatorBtn = doc.querySelector('.ez-filters__item--creator .ez-icon--reset');
    const listGroupsTitle = [...doc.querySelectorAll('.ez-content-type-selector__group-title')];
    const contentTypeCheckboxes = [...doc.querySelectorAll('.ez-content-type-selector__item [type="checkbox"]')];
    const subtreeInput = doc.querySelector('#search_subtree');
    const clearFilters = (event) => {
        event.preventDefault();

        const option = contentTypeSelect.querySelector('option');
        const defaultText = option.dataset.default;
        const lastModifiedModal = doc.querySelector(lastModifiedSelect.dataset.targetSelector);
        const lastCreatedModal = doc.querySelector(lastCreatedSelect.dataset.targetSelector);
        const lastModifiedPeriod = doc.querySelector(lastModifiedModal.dataset.periodSelector);
        const lastModifiedEnd = doc.querySelector(lastModifiedModal.dataset.endSelector);
        const lastCreatedPeriod = doc.querySelector(lastCreatedModal.dataset.periodSelector);
        const lastCreatedEnd = doc.querySelector(lastCreatedModal.dataset.endSelector);

        option.innerHTML = defaultText;
        contentTypeCheckboxes.forEach((checkbox) => {
            checkbox.removeAttribute('checked');
            checkbox.checked = false;
        });

        if (sectionSelect) {
            sectionSelect[0].selected = true;
        }

        lastModifiedSelect[0].selected = true;
        lastCreatedSelect[0].selected = true;
        lastModifiedSelect.querySelector('option').selected = true;
        lastModifiedPeriod.value = '';
        lastModifiedEnd.value = '';
        lastCreatedPeriod.value = '';
        lastCreatedEnd.value = '';
        subtreeInput.value = '';

        handleResetUser();

        event.target.closest('form').submit();
    };
    const toggleDisabledStateOnApplyBtn = () => {
        const contentTypeOption = contentTypeSelect.querySelector('option');
        const isContentTypeSelected = contentTypeOption.innerHTML !== contentTypeOption.dataset.default;
        const isSectionSelected = sectionSelect ? !!sectionSelect.value : false;
        const isModifiedSelected = !!lastModifiedSelect.value;
        const isCreatedSelected = !!lastCreatedSelect.value;
        const isCreatorSelected = !!searchCreatorInput.value;
        const isSubtreeSelected = !!subtreeInput.value.trim().length;
        const isEnabled = isContentTypeSelected || isSectionSelected || isModifiedSelected || isCreatedSelected || isCreatorSelected || isSubtreeSelected;
        const methodName = isEnabled ? 'removeAttribute' : 'setAttribute';

        applyBtn[methodName]('disabled', !isEnabled);
    };
    const toggleFiltersVisibility = (event) => {
        event.preventDefault();

        filters.classList.toggle('ez-filters--collapsed');
    };
    const handleClickOutside = (event) => {
        if (event.target.closest('.ez-content-type-selector') || event.target.closest('.ez-filters__select--content-type')) {
            return;
        }

        toggleContentTypeSelectorVisibility();
    };
    const toggleContentTypeSelectorVisibility = (event) => {
        event.preventDefault();

        const methodName = contentTypeSelector.classList.contains('ez-content-type-selector--collapsed')
            ? 'addEventListener'
            : 'removeEventListener';

        contentTypeSelector.classList.toggle('ez-content-type-selector--collapsed');
        doc.querySelector('body')[methodName]('click', handleClickOutside, false);
    };
    const toggleModalVisibility = (event) => {
        const modal = $(event.target.dataset.targetSelector);

        if (event.target.value !== 'custom_range') {
            doc.querySelector(modal[0].dataset.periodSelector).value = event.target.value;
            doc.querySelector(modal[0].dataset.endSelector).value = '';

            toggleDisabledStateOnApplyBtn();

            return;
        }

        modal.modal('show');
    };
    const toggleGroupState = (event) => {
        event.preventDefault();

        event.currentTarget.closest('.ez-content-type-selector__group').classList.toggle('ez-content-type-selector__group--collapsed');
    };
    const filterByContentType = () => {
        const selectedCheckboxes = contentTypeCheckboxes.filter((checkbox) => checkbox.checked);
        const contentTypesText = selectedCheckboxes.map((checkbox) => checkbox.dataset.name).join(', ');
        const option = contentTypeSelect[0];
        const defaultText = option.dataset.default;

        option.innerHTML = contentTypesText || defaultText;

        toggleDisabledStateOnApplyBtn();
    };
    const dateConfig = {
        formatDate: (date) => new Date(date).toLocaleDateString(),
    };
    const checkSelectFieldsFilled = (modal) => {
        const inputs = [...modal.querySelectorAll('.ez-date-select')];
        const isFilled = inputs.every((input) => !!doc.querySelector(input.dataset.targetSelector).value.trim());
        const methodName = isFilled ? 'removeAttribute' : 'setAttribute';

        modal.querySelector('.ez-btn--select')[methodName]('disabled', !isFilled);
    };
    const setSelectedDateRange = (event) => {
        const modal = event.target.closest('.ez-modal');
        const startInput = modal.querySelector('.ez-date-select--start');
        const targetStartInput = doc.querySelector(startInput.dataset.targetSelector);
        const endInput = modal.querySelector('.ez-date-select--end');
        const targetEndInput = doc.querySelector(endInput.dataset.targetSelector);
        const startDate = parseInt(targetStartInput.value, 10);
        const endDate = parseInt(targetEndInput.value, 10);
        const datePeriod = endDate - startDate;
        const secondsInDay = 60 * 60 * 24;
        const days = datePeriod / secondsInDay;

        doc.querySelector(modal.dataset.periodSelector).value = `P0Y0M${days}D`;
        doc.querySelector(modal.dataset.endSelector).value = endDate;

        toggleDisabledStateOnApplyBtn();
    };
    const updateSourceInputValue = (sourceInput, date) => {
        if (!date.length) {
            sourceInput.value = '';
            sourceInput.dispatchEvent(event);

            return;
        }

        date = new Date(date[0]);
        date = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));

        sourceInput.value = Math.floor(date.getTime() / 1000);

        checkSelectFieldsFilled(sourceInput.closest('.ez-modal'));
    };
    const initFlatPickr = (field) => {
        const sourceInput = doc.querySelector(field.dataset.targetSelector);
        const flatPickrInput = field;
        let defaultDate;

        if (sourceInput.value) {
            defaultDate = new Date(sourceInput.value * 1000);
        }

        global.flatpickr(
            flatPickrInput,
            Object.assign({}, dateConfig, {
                onChange: updateSourceInputValue.bind(null, sourceInput),
                defaultDate,
            })
        );
    };
    const getUsersList = (value) => {
        const body = JSON.stringify({
            ViewInput: {
                identifier: `find-user-by-name-${value}`,
                public: false,
                ContentQuery: {
                    Criteria: {},
                    FacetBuilders: {},
                    SortClauses: {},
                    Query: {
                        FullTextCriterion: `${value}*`,
                        ContentTypeIdentifierCriterion: creatorInput.dataset.contentTypeIdentifiers,
                    },
                    limit: 50,
                    offset: 0,
                },
            },
        });
        const request = new Request('/api/ezp/v2/views', {
            method: 'POST',
            headers: {
                Accept: 'application/vnd.ez.api.View+json; version=1.1',
                'Content-Type': 'application/vnd.ez.api.ViewInput+json; version=1.1',
                'X-Siteaccess': siteaccess,
                'X-CSRF-Token': token,
            },
            body,
            mode: 'same-origin',
            credentials: 'same-origin',
        });

        fetch(request)
            .then((response) => response.json())
            .then(showUsersList);
    };
    const createUsersListItem = (user) =>
        `<li data-id="${user._id}" data-name="${user.Name}" class="ez-filters__user-item">${user.Name}</li>`;
    const showUsersList = (data) => {
        const hits = data.View.Result.searchHits.searchHit;
        const users = hits.reduce((total, hit) => total + createUsersListItem(hit.value.Content), '');
        const methodName = users ? 'addEventListener' : 'removeEventListener';

        usersList.innerHTML = users;
        usersList.classList.remove('ez-filters__user-list--hidden');

        doc.querySelector('body')[methodName]('click', handleClickOutsideUserList, false);
    };
    const handleTyping = (event) => {
        const value = event.target.value.trim();

        window.clearTimeout(getUsersTimeout);

        if (value.length > 2) {
            getUsersTimeout = window.setTimeout(getUsersList.bind(null, value), 200);
        } else {
            usersList.classList.add('ez-filters__user-list--hidden');
            doc.querySelector('body').removeEventListener('click', handleClickOutsideUserList, false);
        }
    };
    const handleSelectUser = (event) => {
        searchCreatorInput.value = event.target.dataset.id;

        usersList.classList.add('ez-filters__user-list--hidden');

        creatorInput.value = event.target.dataset.name;
        creatorInput.setAttribute('disabled', true);

        doc.querySelector('body').removeEventListener('click', handleClickOutsideUserList, false);

        toggleDisabledStateOnApplyBtn();
    };
    const handleResetUser = () => {
        searchCreatorInput.value = '';

        creatorInput.value = '';
        creatorInput.removeAttribute('disabled');

        toggleDisabledStateOnApplyBtn();
    };
    const handleClickOutsideUserList = (event) => {
        if (event.target.closest('.ez-filters__item--creator')) {
            return;
        }

        creatorInput.value = '';
        usersList.classList.add('ez-filters__user-list--hidden');
        doc.querySelector('body').removeEventListener('click', handleClickOutsideUserList, false);
    };

    dateFields.forEach(initFlatPickr);

    filterByContentType();

    clearBtn.addEventListener('click', clearFilters, false);
    filterBtn.addEventListener('click', toggleFiltersVisibility, false);
    contentTypeSelect.addEventListener('mousedown', toggleContentTypeSelectorVisibility, false);

    if (sectionSelect) {
        sectionSelect.addEventListener('change', toggleDisabledStateOnApplyBtn, false);
    }

    subtreeInput.addEventListener('change', toggleDisabledStateOnApplyBtn, false);
    lastModifiedSelect.addEventListener('change', toggleModalVisibility, false);
    lastCreatedSelect.addEventListener('change', toggleModalVisibility, false);
    creatorInput.addEventListener('keyup', handleTyping, false);
    usersList.addEventListener('click', handleSelectUser, false);
    resetCreatorBtn.addEventListener('click', handleResetUser, false);
    listGroupsTitle.forEach((group) => group.addEventListener('click', toggleGroupState, false));
    contentTypeCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', filterByContentType, false));
    selectBtns.forEach((btn) => btn.addEventListener('click', setSelectedDateRange, false));
})(window, document, window.jQuery);
