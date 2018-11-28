(function(global, doc, eZ, React, ReactDOM, Translator) {
    const btns = doc.querySelectorAll('.ez-btn--udw-select-location');
    const udwContainer = doc.getElementById('react-udw');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const findLocationsByIdList = (idList, callback) => {
        const body = JSON.stringify({
            ViewInput: {
                identifier: `udw-locations-by-path-string-${idList.join('-')}`,
                public: false,
                LocationQuery: {
                    Criteria: {},
                    FacetBuilders: {},
                    SortClauses: { SectionIdentifier: 'ascending' },
                    Filter: { LocationIdCriterion: idList.join(',') },
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
                'X-Requested-With': 'XMLHttpRequest',
                'X-Siteaccess': siteaccess,
                'X-CSRF-Token': token,
            },
            body,
            mode: 'same-origin',
            credentials: 'same-origin',
        });
        const errorMessage = Translator.trans(
            /*@Desc("Cannot find children locations with given id - %idList%")*/ 'select_location.error',
            { idList: idList.join(',') },
            'universal_discovery_widget'
        );

        fetch(request)
            .then(eZ.helpers.request.getJsonFromResponse)
            .then(callback)
            .catch(() => eZ.helpers.notification.showErrorNotification(errorMessage));
    };
    const removeRootFromPathString = (pathString) => {
        const pathArray = pathString.split('/').filter((val) => val);

        return pathArray.splice(1, pathArray.length - 1);
    };
    const buildBreadcrumbsString = (viewData) => {
        const searchHitList = viewData.View.Result.searchHits.searchHit;

        return searchHitList.map((searchHit) => searchHit.value.Location.ContentInfo.Content.Name).join(' / ');
    };
    const toggleVisibility = (btn, isLocationSelected) => {
        const contentBreadcrumbsWrapper = doc.querySelector(btn.dataset.contentBreadcrumbsSelector);

        btn.hidden = isLocationSelected;

        if (contentBreadcrumbsWrapper) {
            contentBreadcrumbsWrapper.hidden = !isLocationSelected;
        }
    };
    const updateBreadcrumbsState = (btn, pathString) => {
        const pathStringInput = doc.querySelector(btn.dataset.locationPathInputSelector);
        const contentBreadcrumbs = doc.querySelector(btn.dataset.contentBreadcrumbsSelector);
        const contentBreadcrumbsContainer = contentBreadcrumbs.querySelector('.ez-filters__breadcrumbs');
        const contentBreadcrumbsSpinner = contentBreadcrumbs.querySelector('.ez-filters__breadcrumbs-spinner');

        pathStringInput.value = pathString;
        pathStringInput.dispatchEvent(new Event('change'));

        if (!contentBreadcrumbsContainer || !contentBreadcrumbsSpinner) {
            return;
        }

        if (!pathString) {
            contentBreadcrumbsContainer.innerHTML = '';
            contentBreadcrumbsContainer.hidden = true;
        } else {
            contentBreadcrumbsSpinner.hidden = false;
            findLocationsByIdList(removeRootFromPathString(pathString), (data) => {
                contentBreadcrumbsContainer.innerHTML = buildBreadcrumbsString(data);
                contentBreadcrumbsSpinner.hidden = true;
                contentBreadcrumbsContainer.hidden = false;
            });
        }
    };
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const onConfirm = (btn, items) => {
        closeUDW();

        const pathString = items[0].pathString;

        updateBreadcrumbsState(btn, pathString);
        toggleVisibility(btn, !!pathString);
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);

        ReactDOM.render(
            React.createElement(
                eZ.modules.UniversalDiscovery,
                Object.assign(
                    {
                        onConfirm: onConfirm.bind(null, event.currentTarget),
                        onCancel,
                        title: event.currentTarget.dataset.universalDiscoveryTitle,
                        multiple: false,
                        startingLocationId: eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
                        restInfo: { token, siteaccess },
                    },
                    config
                )
            ),
            udwContainer
        );
    };
    const clearSelection = (btn) => {
        updateBreadcrumbsState(btn, '');
        toggleVisibility(btn, false);
    };

    btns.forEach((btn) => {
        btn.addEventListener('click', openUDW, false);

        const clearBtn = doc.querySelector(btn.dataset.clearBtnSelector);

        if (clearBtn) {
            clearBtn.addEventListener('click', clearSelection.bind(null, btn), false);
        }
    });
})(window, document, window.eZ, window.React, window.ReactDOM, window.Translator);
