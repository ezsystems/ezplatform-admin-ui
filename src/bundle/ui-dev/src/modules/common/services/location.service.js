import { handleRequestResponse, ENDPOINT_VIEWS, HEADERS_VIEWS } from './common.service';

export const loadLocation = (locationId = 2, limit = 10, offset = 0, callback) => {
    const body = JSON.stringify({
        ViewInput: {
            identifier: `subitems-load-location-${locationId}`,
            public: false,
            LocationQuery: {
                Criteria: {},
                FacetBuilders: {},
                SortClauses: { LocationPriority: 'ascending' },
                Filter: { ParentLocationIdCriterion: locationId },
                limit,
                offset,
            },
        },
    });

    const request = new Request(ENDPOINT_VIEWS, {
        method: 'POST',
        headers: HEADERS_VIEWS,
        body,
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then(callback)
        .catch(() => window.eZ.helpers.notification.showErrorNotification('Cannot load location'));
};

export const findLocationsByParentLocationId = (parentLocationId, callback) => {
    const body = JSON.stringify({
        ViewInput: {
            identifier: `udw-locations-by-parent-location-id-${parentLocationId}`,
            public: false,
            LocationQuery: {
                Criteria: {},
                FacetBuilders: {},
                SortClauses: { SectionIdentifier: 'ascending' },
                Filter: { ParentLocationIdCriterion: parentLocationId },
                limit: 50,
                offset: 0,
            },
        },
    });

    const request = new Request(ENDPOINT_VIEWS, {
        method: 'POST',
        headers: HEADERS_VIEWS,
        body,
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then((json) => callback({ parentLocationId, data: json }))
        .catch(() => window.eZ.helpers.notification.showErrorNotification('Cannot find children locations by a parent location id'));
};
