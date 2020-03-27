import { handleRequestResponse, ENDPOINT_VIEWS, HEADERS_VIEWS } from './common.service';

export const loadContentInfo = (contentIds, callback) => {
    const ids = contentIds.join();
    const body = JSON.stringify({
        ViewInput: {
            identifier: `subitems-load-content-info-${ids}`,
            public: false,
            ContentQuery: {
                Criteria: {},
                FacetBuilders: {},
                SortClauses: {},
                Filter: { ContentIdCriterion: `${ids}` },
                limit: contentIds.length,
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
        .then(callback)
        .catch(() => window.eZ.helpers.notification.showErrorNotification('Cannot load content info'));
};

export const findContentBySearchQuery = (query, callback) => {
    const body = JSON.stringify({
        ViewInput: {
            identifier: `udw-locations-by-search-query-${query}`,
            public: false,
            LocationQuery: {
                Criteria: {},
                FacetBuilders: {},
                SortClauses: {},
                Filter: { FullTextCriterion: query },
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
        .then(callback)
        .catch(() => window.eZ.helpers.notification.showErrorNotification('Cannot find content by a search query'));
};
