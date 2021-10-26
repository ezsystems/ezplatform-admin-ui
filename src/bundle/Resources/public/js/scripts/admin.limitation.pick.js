(function(global, doc, eZ, React, ReactDOM, Translator) {
    const SELECTOR_LOCATION_LIMITATION_BTN = '.ez-pick-location-limitation-button';
    const SELECTOR_EZ_TAG = '.ibexa-tag';
    const IDS_SEPARATOR = ',';
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const udwContainer = doc.getElementById('react-udw');
    const limitationBtns = doc.querySelectorAll(SELECTOR_LOCATION_LIMITATION_BTN);
    const findLocationsByIdList = (pathArraysWithoutRoot, callback) => {
        const bulkOperations = getBulkOperations(pathArraysWithoutRoot);
        const request = new Request('/api/ezp/v2/bulk', {
            method: 'POST',
            headers: {
                Accept: 'application/vnd.ez.api.BulkOperationResponse+json',
                'Content-Type': 'application/vnd.ez.api.BulkOperation+json',
                'X-Siteaccess': siteaccess,
                'X-CSRF-Token': token,
            },
            body: JSON.stringify({
                bulkOperations: {
                    operations: bulkOperations,
                },
            }),
            mode: 'same-origin',
            credentials: 'same-origin',
        });
        const errorMessage = Translator.trans(
            /*@Desc("Could not fetch content names")*/ 'limitation.pick.error',
            {},
            'universal_discovery_widget'
        );

        fetch(request)
            .then(eZ.helpers.request.getJsonFromResponse)
            .then(callback)
            .catch(() => eZ.helpers.notification.showErrorNotification(errorMessage));
    };
    const getBulkOperations = (pathArraysWithoutRoot) =>
        pathArraysWithoutRoot.reduce((operations, pathArray) => {
            const locationId = pathArray[pathArray.length - 1];

            operations[locationId] = {
                uri: '/api/ezp/v2/views',
                method: 'POST',
                headers: {
                    Accept: 'application/vnd.ez.api.View+json; version=1.1',
                    'Content-Type': 'application/vnd.ez.api.ViewInput+json; version=1.1',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                content: JSON.stringify({
                    ViewInput: {
                        identifier: `udw-locations-by-path-string-${pathArray.join('-')}`,
                        public: false,
                        LocationQuery: {
                            FacetBuilders: {},
                            SortClauses: { SectionIdentifier: 'ascending' },
                            Filter: { LocationIdCriterion: pathArray.join(IDS_SEPARATOR) },
                            limit: 50,
                            offset: 0,
                        },
                    },
                }),
            };

            return operations;
        }, {});
    const removeRootLocation = (pathArray) => pathArray.slice(1);
    const pathStringToPathArray = (pathString) => pathString.split('/').filter((el) => el);
    const buildContentBreadcrumbs = (viewData) => {
        const searchHitList = viewData.View.Result.searchHits.searchHit;

        return searchHitList.map((searchHit) => searchHit.value.Location.ContentInfo.Content.TranslatedName).join(' / ');
    };
    const addLocationsToInput = (limitationBtn, selectedItems) => {
        const input = doc.querySelector(limitationBtn.dataset.locationInputSelector);
        const selectedLocationsIds = selectedItems.map((item) => item.id).join(IDS_SEPARATOR);

        input.value = selectedLocationsIds;
    };
    const removeLocationFromInput = (locationInputSelector, removedLocationId) => {
        const input = doc.querySelector(locationInputSelector);
        const locationsIdsWithoutRemoved = input.value.split(IDS_SEPARATOR).filter((locationId) => locationId !== removedLocationId);

        input.value = locationsIdsWithoutRemoved.join(IDS_SEPARATOR);
    };
    const addLocationsTags = (limitationBtn, selectedItems) => {
        const tagsList = doc.querySelector(limitationBtn.dataset.selectedLocationListSelector);
        const tagTemplate = limitationBtn.dataset.valueTemplate;
        const fragment = doc.createDocumentFragment();

        selectedItems.forEach((location) => {
            const locationId = location.id;
            const container = doc.createElement('ul');
            const renderedItem = tagTemplate.replace('{{ location_id }}', locationId);

            container.insertAdjacentHTML('beforeend', renderedItem);

            const listItemNode = container.querySelector('li');
            const tagNode = listItemNode.querySelector(SELECTOR_EZ_TAG);

            attachTagEventHandlers(limitationBtn, tagNode);
            fragment.append(listItemNode);
        });

        tagsList.innerHTML = '';
        tagsList.append(fragment);

        setTagsBreadcrumbs(tagsList, selectedItems);
    };
    const setTagsBreadcrumbs = (tagsList, selectedItems) => {
        const pathArraysWithoutRoot = selectedItems.map(getLocationPathArray);

        findLocationsByIdList(pathArraysWithoutRoot, (response) => {
            const { operations } = response.BulkOperationResponse;

            Object.entries(operations).forEach(([locationId, { content }]) => {
                const viewData = JSON.parse(content);
                const tag = tagsList.querySelector(`[data-location-id="${locationId}"]`);
                const tagContent = tag.querySelector('.ibexa-tag__content');
                const tagSpinner = tag.querySelector('.ibexa-tag__spinner');

                tagContent.innerText = buildContentBreadcrumbs(viewData);

                tagSpinner.hidden = true;
                tagContent.hidden = false;
            });
        });
    };
    const getLocationPathArray = ({ pathString }) => {
        const pathArray = pathStringToPathArray(pathString);
        const pathArrayWithoutRoot = removeRootLocation(pathArray);

        return pathArrayWithoutRoot;
    };
    const handleTagRemove = (limitationBtn, tag) => {
        const removedLocationId = tag.dataset.locationId;
        const locationInputSelector = limitationBtn.dataset.locationInputSelector;

        removeLocationFromInput(locationInputSelector, removedLocationId);
        tag.remove();
    };
    const attachTagEventHandlers = (limitationBtn, tag) => {
        const removeTagBtn = tag.querySelector('.ibexa-tag__remove-btn');

        removeTagBtn.addEventListener('click', () => handleTagRemove(limitationBtn, tag), false);
    };
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const handleUdwConfirm = (limitationBtn, selectedItems) => {
        if (selectedItems.length) {
            addLocationsToInput(limitationBtn, selectedItems);
            addLocationsTags(limitationBtn, selectedItems);
        }

        closeUDW();
    };
    const openUDW = (event) => {
        event.preventDefault();

        const limitationBtn = event.currentTarget;
        const input = doc.querySelector(limitationBtn.dataset.locationInputSelector);
        const selectedLocationsIds = input.value
            .split(IDS_SEPARATOR)
            .filter((idString) => !!idString)
            .map((idString) => parseInt(idString, 10));
        const config = JSON.parse(event.currentTarget.dataset.udwConfig);
        const title = Translator.trans(/*@Desc("Choose Locations")*/ 'subtree_limitation.title', {}, 'universal_discovery_widget');

        ReactDOM.render(
            React.createElement(eZ.modules.UniversalDiscovery, {
                onConfirm: handleUdwConfirm.bind(this, event.target),
                onCancel: closeUDW,
                title,
                multiple: true,
                selectedLocations: selectedLocationsIds,
                ...config,
            }),
            udwContainer
        );
    };

    limitationBtns.forEach((limitationBtn) => {
        const tagsList = doc.querySelector(limitationBtn.dataset.selectedLocationListSelector);
        const tags = tagsList.querySelectorAll(SELECTOR_EZ_TAG);

        tags.forEach(attachTagEventHandlers.bind(null, limitationBtn));
        limitationBtn.addEventListener('click', openUDW, false);
    });
})(window, window.document, window.eZ, window.React, window.ReactDOM, window.Translator);
