(function (global, doc, eZ, $, flatpickr) {
    let getUsersTimeout;
    const CLASS_DATE_RANGE = 'ez-filters__range-wrapper';
    const CLASS_VISIBLE_DATE_RANGE = 'ez-filters__range-wrapper--visible';
    const SELECTOR_TAG = '.ez-tag';
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const filterBtn = doc.querySelector('.ez-btn--filter');
    const filters = doc.querySelector('.ez-filters');
    const searchCriteriaTags = doc.querySelector('.ez-search-criteria-tags');
    const clearBtn = filters.querySelector('.ez-btn-clear');
    const applyBtn = filters.querySelector('.ez-btn-apply');
    const dateFields = doc.querySelectorAll('.ez-filters__range-select');
    const contentTypeSelector = doc.querySelector('.ez-content-type-selector');
    const contentTypeSelect = doc.querySelector('.ez-filters__item--content-type .ez-filters__select');
    const sectionSelect = doc.querySelector('.ez-filters__item--section .ez-filters__select');
    const lastModifiedSelect = doc.querySelector('.ez-filters__item--modified .ez-filters__select');
    const lastModifiedDateRange = doc.querySelector('.ez-filters__item--modified .ez-filters__range-select');
    const lastCreatedSelect = doc.querySelector('.ez-filters__item--created .ez-filters__select');
    const lastCreatedDateRange = doc.querySelector('.ez-filters__item--created .ez-filters__range-select');
    const creatorInput = doc.querySelector('.ez-filters__item--creator .ez-filters__input');
    const searchCreatorInput = doc.querySelector('#search_creator');
    const usersList = doc.querySelector('.ez-filters__item--creator .ez-filters__user-list');
    const resetCreatorBtn = doc.querySelector('.ez-filters__item--creator .ez-icon--reset');
    const listGroupsTitle = doc.querySelectorAll('.ez-content-type-selector__group-title');
    const contentTypeCheckboxes = doc.querySelectorAll('.ez-content-type-selector__item [type="checkbox"]');
    const subtreeInput = doc.querySelector('#search_subtree');
    const dateConfig = {
        mode: 'range',
        locale: {
            rangeSeparator: ' - ',
        },
        formatDate: (date) => eZ.helpers.timezone.formatShortDateTime(date, null, eZ.adminUiConfig.dateFormat.shortDate),
    };
    const clearFilters = (event) => {
        event.preventDefault();

        const option = contentTypeSelect.querySelector('option');
        const defaultText = option.dataset.default;
        const lastModifiedDataRange = doc.querySelector(lastModifiedSelect.dataset.targetSelector);
        const lastCreatedDataRange = doc.querySelector(lastCreatedSelect.dataset.targetSelector);
        const lastModifiedPeriod = doc.querySelector(lastModifiedDataRange.dataset.periodSelector);
        const lastModifiedEnd = doc.querySelector(lastModifiedDataRange.dataset.endSelector);
        const lastCreatedPeriod = doc.querySelector(lastCreatedDataRange.dataset.periodSelector);
        const lastCreatedEnd = doc.querySelector(lastCreatedDataRange.dataset.endSelector);

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
        const isCreatorSelected = !!searchCreatorInput.value;
        const isSubtreeSelected = !!subtreeInput.value.trim().length;
        let isModifiedSelected = !!lastModifiedSelect.value;
        let isCreatedSelected = !!lastCreatedSelect.value;

        if (lastModifiedSelect.value === 'custom_range') {
            const lastModifiedWrapper = lastModifiedDateRange.closest(`.${CLASS_DATE_RANGE}`);
            const { periodSelector, endSelector } = lastModifiedWrapper.dataset;
            const lastModifiedPeriodValue = doc.querySelector(periodSelector).value;
            const lastModifiedEndDate = doc.querySelector(endSelector).value;

            if (!lastModifiedPeriodValue || !lastModifiedEndDate) {
                isModifiedSelected = false;
            }
        }

        if (lastCreatedSelect.value === 'custom_range') {
            const lastCreatedWrapper = lastCreatedDateRange.closest(`.${CLASS_DATE_RANGE}`);
            const { periodSelector, endSelector } = lastCreatedWrapper.dataset;
            const lastCreatedPeriodValue = doc.querySelector(periodSelector).value;
            const lastCreatedEndDate = doc.querySelector(endSelector).value;

            if (!lastCreatedPeriodValue || !lastCreatedEndDate) {
                isCreatedSelected = false;
            }
        }

        const isEnabled =
            isContentTypeSelected || isSectionSelected || isModifiedSelected || isCreatedSelected || isCreatorSelected || isSubtreeSelected;
        const methodName = isEnabled ? 'removeAttribute' : 'setAttribute';

        applyBtn[methodName]('disabled', !isEnabled);
    };
    const toggleFiltersVisibility = (event) => {
        event.preventDefault();

        filters.classList.toggle('ez-filters--collapsed');
        searchCriteriaTags.classList.toggle('ez-search-criteria-tags--collapsed');
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
    const toggleDatesSelectVisibility = (event) => {
        const datesRangeNode = doc.querySelector(event.target.dataset.targetSelector);

        if (event.target.value !== 'custom_range') {
            doc.querySelector(datesRangeNode.dataset.periodSelector).value = event.target.value;
            doc.querySelector(datesRangeNode.dataset.endSelector).value = '';
            datesRangeNode.classList.remove(CLASS_VISIBLE_DATE_RANGE);

            toggleDisabledStateOnApplyBtn();

            return;
        }

        datesRangeNode.classList.add(CLASS_VISIBLE_DATE_RANGE);
    };
    const toggleGroupState = (event) => {
        event.preventDefault();

        event.currentTarget.closest('.ez-content-type-selector__group').classList.toggle('ez-content-type-selector__group--collapsed');
    };
    const filterByContentType = () => {
        const selectedCheckboxes = [...contentTypeCheckboxes].filter((checkbox) => checkbox.checked);
        const contentTypesText = selectedCheckboxes.map((checkbox) => checkbox.dataset.name).join(', ');
        const option = contentTypeSelect[0];
        const defaultText = option.dataset.default;

        option.innerHTML = contentTypesText || defaultText;

        toggleDisabledStateOnApplyBtn();
    };
    const setSelectedDateRange = (selectedDates, dateString, instance) => {
        const dateRange = instance.input.closest('.ez-filters__range-wrapper');

        if (selectedDates.length === 2) {
            const startDate = getUnixTimestampUTC(selectedDates[0]);
            const endDate = getUnixTimestampUTC(selectedDates[1]);
            const secondsInDay = 86400;
            const days = (endDate - startDate) / secondsInDay;

            doc.querySelector(dateRange.dataset.periodSelector).value = `P0Y0M${days}D`;
            doc.querySelector(dateRange.dataset.endSelector).value = endDate;
        }

        toggleDisabledStateOnApplyBtn();
    };
    const getUnixTimestampUTC = (dateObject) => {
        let date = new Date(Date.UTC(dateObject.getFullYear(), dateObject.getMonth(), dateObject.getDate()));

        date = Math.floor(date.getTime() / 1000);

        return date;
    };
    const getUsersList = (value) => {
        const body = JSON.stringify({
            ViewInput: {
                identifier: `find-user-by-name-${value}`,
                public: false,
                ContentQuery: {
                    FacetBuilders: {},
                    SortClauses: {},
                    Query: {
                        FullTextCriterion: `${value}*`,
                        ContentTypeIdentifierCriterion: creatorInput.dataset.contentTypeIdentifiers.split(','),
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
    const createUsersListItem = (user) => {
        return `<li data-id="${user._id}" data-name="${user.TranslatedName}" class="ez-filters__user-item">${user.TranslatedName}</li>`;
    };
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
    const initFlatPickr = (dateRangePickerNode) => {
        const { start, end } = dateRangePickerNode.dataset;
        const defaultDate = start && end ? [start, end] : [];

        flatpickr(dateRangePickerNode, {
            ...dateConfig,
            onChange: setSelectedDateRange,
            defaultDate,
        });
    };
    const removeSearchTag = (event) => {
        const tag = event.currentTarget.closest(SELECTOR_TAG);
        const form = event.currentTarget.closest('form');

        eZ.helpers.tooltips.hideAll();
        tag.remove();
        form.submit();
    };
    const clearContentType = (event) => {
        const checkbox = doc.querySelector(event.currentTarget.dataset.targetSelector);

        checkbox.checked = false;
        removeSearchTag(event);
    };
    const clearSection = (event) => {
        sectionSelect[0].selected = true;
        removeSearchTag(event);
    };
    const clearSubtree = (event) => {
        doc.querySelector('#search_subtree-content-breadcrumbs').hidden = true;
        doc.querySelector('.ez-btn--udw-select-location').hidden = false;
        subtreeInput.value = '';
        removeSearchTag(event);
    };
    const clearDataRange = (event, selector) => {
        const dataRange = doc.querySelector(selector);
        const rangeSelect = dataRange.parentNode.querySelector('.ez-filters__select');
        const periodInput = doc.querySelector(dataRange.dataset.periodSelector);
        const endDateInput = doc.querySelector(dataRange.dataset.endSelector);

        rangeSelect[0].selected = true;
        periodInput.value = '';
        endDateInput.vaue = '';
        dataRange.classList.remove(CLASS_VISIBLE_DATE_RANGE);
        removeSearchTag(event);
    };
    const clearCreator = (event) => {
        handleResetUser();
        removeSearchTag(event);
    };
    const clearSearchTagBtnMethods = {
        section: (event) => clearSection(event),
        subtree: (event) => clearSubtree(event),
        creator: (event) => clearCreator(event),
        'content-types': (event) => clearContentType(event),
        'last-modified': (event) => clearDataRange(event, lastModifiedSelect.dataset.targetSelector),
        'last-created': (event) => clearDataRange(event, lastCreatedSelect.dataset.targetSelector),
    };

    dateFields.forEach(initFlatPickr);
    filterByContentType();

    clearBtn.addEventListener('click', clearFilters, false);
    filterBtn.addEventListener('click', toggleFiltersVisibility, false);
    contentTypeSelect.addEventListener('mousedown', toggleContentTypeSelectorVisibility, false);

    if (sectionSelect) {
        sectionSelect.addEventListener('change', toggleDisabledStateOnApplyBtn, false);
    }

    for (const tagType in clearSearchTagBtnMethods) {
        const tagBtns = doc.querySelectorAll(`.ez-tag__remove-btn--${tagType}`);

        tagBtns.forEach((btn) => btn.addEventListener('click', clearSearchTagBtnMethods[tagType], false));
    }

    subtreeInput.addEventListener('change', toggleDisabledStateOnApplyBtn, false);
    lastModifiedSelect.addEventListener('change', toggleDatesSelectVisibility, false);
    lastCreatedSelect.addEventListener('change', toggleDatesSelectVisibility, false);
    creatorInput.addEventListener('keyup', handleTyping, false);
    usersList.addEventListener('click', handleSelectUser, false);
    resetCreatorBtn.addEventListener('click', handleResetUser, false);
    listGroupsTitle.forEach((group) => group.addEventListener('click', toggleGroupState, false));
    contentTypeCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', filterByContentType, false));
})(window, window.document, window.eZ, window.jQuery, window.flatpickr);
