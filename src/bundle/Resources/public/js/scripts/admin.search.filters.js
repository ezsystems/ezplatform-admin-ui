(function(global, doc, eZ, flatpickr, React, ReactDOM) {
    let getUsersTimeout;
    const CLASS_DATE_RANGE = 'ibexa-filters__range-wrapper';
    const CLASS_VISIBLE_DATE_RANGE = 'ibexa-filters__range-wrapper--visible';
    const SELECTOR_TAG = '.ibexa-tag';
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const filters = doc.querySelector('.ibexa-filters');
    const clearBtn = filters.querySelector('.ibexa-btn--clear');
    const applyBtn = filters.querySelector('.ibexa-btn--apply');
    const dateFields = doc.querySelectorAll('.ibexa-filters__range-select');
    const contentTypeSelect = doc.querySelector('.ibexa-filters__item--content-type .ibexa-filters__select');
    const sectionSelect = doc.querySelector('.ibexa-filters__item--section .ibexa-filters__select');
    const lastModifiedSelect = doc.querySelector('.ibexa-filters__item--modified .ibexa-filters__select');
    const lastModifiedDateRange = doc.querySelector('.ibexa-filters__item--modified .ibexa-filters__range-select');
    const lastCreatedSelect = doc.querySelector('.ibexa-filters__item--created .ibexa-filters__select');
    const lastCreatedDateRange = doc.querySelector('.ibexa-filters__item--created .ibexa-filters__range-select');
    const creatorInput = doc.querySelector('.ibexa-filters__item--creator .ibexa-filters__input');
    const searchCreatorInput = doc.querySelector('#search_creator');
    const usersList = doc.querySelector('.ibexa-filters__item--creator .ibexa-filters__user-list');
    const resetCreatorBtn = doc.querySelector('.ibexa-filters__item--creator .ibexa-icon--reset');
    const contentTypeCheckboxes = doc.querySelectorAll('.ibexa-content-type-selector__item [type="checkbox"]');
    const selectSubtreeBtn = doc.querySelector('.ibexa-filters__item--subtree .ibexa-tag-view-select__btn-select-path');
    const subtreeInput = doc.querySelector('#search_subtree');
    const showMoreBtns = doc.querySelectorAll('.ibexa-content-type-selector__show-more');
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
    const filterByContentType = () => {
        const selectedCheckboxes = [...contentTypeCheckboxes].filter((checkbox) => checkbox.checked);
        const contentTypesText = selectedCheckboxes.map((checkbox) => checkbox.dataset.name).join(', ');
        const option = contentTypeSelect[0];
        const defaultText = option.dataset.default;

        option.innerHTML = contentTypesText || defaultText;

        toggleDisabledStateOnApplyBtn();
    };
    const setSelectedDateRange = (selectedDates, dateString, instance) => {
        const dateRange = instance.input.closest('.ibexa-filters__range-wrapper');

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
        return `<li data-id="${user._id}" data-name="${user.TranslatedName}" class="ibexa-filters__user-item">${user.TranslatedName}</li>`;
    };
    const showUsersList = (data) => {
        const hits = data.View.Result.searchHits.searchHit;
        const users = hits.reduce((total, hit) => total + createUsersListItem(hit.value.Content), '');
        const methodName = users ? 'addEventListener' : 'removeEventListener';

        usersList.innerHTML = users;
        usersList.classList.remove('ibexa-filters__user-list--hidden');

        doc.querySelector('body')[methodName]('click', handleClickOutsideUserList, false);
    };
    const handleTyping = (event) => {
        const value = event.target.value.trim();

        window.clearTimeout(getUsersTimeout);

        if (value.length > 2) {
            getUsersTimeout = window.setTimeout(getUsersList.bind(null, value), 200);
        } else {
            usersList.classList.add('ibexa-filters__user-list--hidden');
            doc.querySelector('body').removeEventListener('click', handleClickOutsideUserList, false);
        }
    };
    const handleSelectUser = (event) => {
        searchCreatorInput.value = event.target.dataset.id;

        usersList.classList.add('ibexa-filters__user-list--hidden');

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
        if (event.target.closest('.ibexa-filters__item--creator')) {
            return;
        }

        creatorInput.value = '';
        usersList.classList.add('ibexa-filters__user-list--hidden');
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
        subtreeInput.value = '';
        removeSearchTag(event);
    };
    const clearDataRange = (event, selector) => {
        const dataRange = doc.querySelector(selector);
        const rangeSelect = dataRange.parentNode.querySelector('.ibexa-filters__select');
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
    const showMoreContentTypes = (event) => {
        const btn = event.currentTarget;
        const contentTypesList = btn
            .closest('.ibexa-content-type-selector__list-wrapper')
            .querySelector('.ibexa-content-type-selector__list[hidden]');

        btn.setAttribute('hidden', true);
        contentTypesList.removeAttribute('hidden');
    };
    const selectSubtreeWidget = new eZ.core.TagViewSelect({
        fieldContainer: doc.querySelector('.ibexa-filters__item--subtree'),
    });
    const udwContainer = doc.getElementById('react-udw');
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const confirmSubtreeUDW = (data) => {
        const items = data.map((item) => ({
            id: item.pathString,
            name: eZ.helpers.text.escapeHTML(item.ContentInfo.Content.TranslatedName),
        }));

        selectSubtreeWidget.addItems(items, true);

        closeUDW();
    };
    const openSubtreeUDW = (event) => {
        event.preventDefault();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);

        ReactDOM.render(
            React.createElement(eZ.modules.UniversalDiscovery, {
                onConfirm: confirmSubtreeUDW.bind(this),
                onCancel: closeUDW,
                multiple: true,
                ...config,
            }),
            udwContainer
        );
    };

    dateFields.forEach(initFlatPickr);
    filterByContentType();

    clearBtn.addEventListener('click', clearFilters, false);

    if (sectionSelect) {
        sectionSelect.addEventListener('change', toggleDisabledStateOnApplyBtn, false);
    }

    for (const tagType in clearSearchTagBtnMethods) {
        const tagBtns = doc.querySelectorAll(`.ibexa-tag__remove-btn--${tagType}`);

        tagBtns.forEach((btn) => btn.addEventListener('click', clearSearchTagBtnMethods[tagType], false));
    }

    subtreeInput.addEventListener('change', toggleDisabledStateOnApplyBtn, false);
    lastModifiedSelect.addEventListener('change', toggleDatesSelectVisibility, false);
    lastCreatedSelect.addEventListener('change', toggleDatesSelectVisibility, false);
    creatorInput.addEventListener('keyup', handleTyping, false);
    usersList.addEventListener('click', handleSelectUser, false);
    resetCreatorBtn.addEventListener('click', handleResetUser, false);
    contentTypeCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', filterByContentType, false));
    showMoreBtns.forEach((showMoreBtn) => showMoreBtn.addEventListener('click', showMoreContentTypes, false));
    selectSubtreeBtn.addEventListener('click', openSubtreeUDW, false);
})(window, window.document, window.eZ, window.flatpickr, window.React, window.ReactDOM);
