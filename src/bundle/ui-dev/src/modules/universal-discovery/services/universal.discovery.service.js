import { showErrorNotification } from '../../common/services/notification.service';
import { handleRequestResponse, handleRequestResponseStatus } from '../../common/helpers/request.helper.js';

const HEADERS_CREATE_VIEW = {
    Accept: 'application/vnd.ez.api.View+json; version=1.1',
    'Content-Type': 'application/vnd.ez.api.ViewInput+json; version=1.1',
};
const ENDPOINT_CREATE_VIEW = '/api/ezp/v2/views';
const ENDPOINT_BOOKMARK = '/api/ezp/v2/bookmark';

export const QUERY_LIMIT = 50;

const showErrorNotificationAbortWrapper = (error) => {
    if (error?.name === 'AbortError') {
        return;
    }

    return showErrorNotification(error);
}

const mapSubitems = (subitems) => {
    return subitems.locations.map((location) => {
        const mappedSubitems = {
            location: location.Location,
        };

        if (subitems.versions) {
            const version = subitems.versions.find(
                (version) => version.Version.VersionInfo.Content._href === location.Location.Content._href
            );

            mappedSubitems.version = version.Version;
        }

        return mappedSubitems;
    });
};

export const findLocationsByParentLocationId = (
    { token, parentLocationId, limit = QUERY_LIMIT, offset = 0, sortClause = 'DatePublished', sortOrder = 'ascending', gridView = false },
    callback
) => {
    const routeName = gridView ? 'ezplatform.udw.location_gridview.data' : 'ezplatform.udw.location.data';
    const url = window.Routing.generate(routeName, {
        locationId: parentLocationId,
    });
    const request = new Request(`${url}?limit=${limit}&offset=${offset}&sortClause=${sortClause}&sortOrder=${sortOrder}`, {
        method: 'GET',
        headers: { 'X-CSRF-Token': token },
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then((response) => {
            const { bookmarked, location, permissions, subitems, version } = response;
            const subitemsData = mapSubitems(subitems);
            const locationData = {
                location: location ? location.Location : null,
                version: version ? version.Version : null,
                totalCount: subitems.totalCount,
                subitems: subitemsData,
                bookmarked,
                permissions,
                parentLocationId,
            };

            callback(locationData);
        })
        .catch(showErrorNotificationAbortWrapper);
};

export const loadAccordionData = (
    {
        token,
        parentLocationId,
        limit = QUERY_LIMIT,
        sortClause = 'DatePublished',
        sortOrder = 'ascending',
        gridView = false,
        rootLocationId = 1,
    },
    callback
) => {
    const routeName = gridView ? 'ezplatform.udw.accordion_gridview.data' : 'ezplatform.udw.accordion.data';
    const url = window.Routing.generate(routeName, {
        locationId: parentLocationId,
    });
    const request = new Request(`${url}?limit=${limit}&sortClause=${sortClause}&sortOrder=${sortOrder}&rootLocationId=${rootLocationId}`, {
        method: 'GET',
        headers: { 'X-CSRF-Token': token },
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then((response) => {
            const mappedItems = response.breadcrumb.map((item) => {
                const location = item.Location;
                const itemData = response.columns[location.id];
                const mappedItem = {
                    location,
                    totalCount: itemData ? itemData.subitems.totalCount : undefined,
                    subitems: itemData ? mapSubitems(itemData.subitems) : [],
                    parentLocationId: location.id,
                    collapsed: !response.columns[location.id],
                };

                return mappedItem;
            });

            const rootLocationData = response.columns[1];
            const lastLocationData = response.columns[parentLocationId];

            if (rootLocationData) {
                mappedItems.unshift({
                    totalCount: rootLocationData ? rootLocationData.subitems.totalCount : undefined,
                    subitems: rootLocationData ? mapSubitems(rootLocationData.subitems) : [],
                    parentLocationId: 1,
                    collapsed: false,
                });
            }

            mappedItems.push({
                location: lastLocationData.location.Location,
                version: lastLocationData.version.Version,
                totalCount: lastLocationData ? lastLocationData.subitems.totalCount : undefined,
                subitems: lastLocationData ? mapSubitems(lastLocationData.subitems) : [],
                bookmarked: lastLocationData.bookmarked,
                permissions: lastLocationData.permissions,
                parentLocationId,
            });

            callback(mappedItems);
        })
        .catch(showErrorNotificationAbortWrapper);
};

export const findLocationsBySearchQuery = ({ token, siteaccess, query, limit = QUERY_LIMIT, offset = 0, languageCode = null }, callback) => {
    const useAlwaysAvailable = true;
    const body = JSON.stringify({
        ViewInput: {
            identifier: `udw-locations-by-search-query-${query.FullTextCriterion}`,
            public: false,
            languageCode,
            useAlwaysAvailable,
            LocationQuery: {
                FacetBuilders: {},
                SortClauses: {},
                Query: query,
                limit,
                offset,
            },
        },
    });
    const request = new Request(ENDPOINT_CREATE_VIEW, {
        method: 'POST',
        headers: { ...HEADERS_CREATE_VIEW, 'X-Siteaccess': siteaccess, 'X-CSRF-Token': token },
        body,
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then((response) => {
            const { count, searchHits } = response.View.Result;
            const items = searchHits.searchHit.map((searchHit) => searchHit.value.Location);

            callback({
                items,
                count,
            });
        })
        .catch(showErrorNotificationAbortWrapper);
};

export const findLocationsById = ({ token, siteaccess, id, limit = QUERY_LIMIT, offset = 0 }, callback) => {
    const body = JSON.stringify({
        ViewInput: {
            identifier: `udw-locations-by-id-${id}`,
            public: false,
            LocationQuery: {
                FacetBuilders: {},
                SortClauses: { SectionIdentifier: 'ascending' },
                Filter: { LocationIdCriterion: id },
                limit,
                offset,
            },
        },
    });
    const request = new Request(ENDPOINT_CREATE_VIEW, {
        method: 'POST',
        headers: { ...HEADERS_CREATE_VIEW, 'X-Siteaccess': siteaccess, 'X-CSRF-Token': token },
        body,
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then((response) => {
            const items = response.View.Result.searchHits.searchHit.map((searchHit) => searchHit.value.Location);

            callback(items);
        })
        .catch(showErrorNotificationAbortWrapper);
};

export const findContentInfo = ({ token, siteaccess, contentId, limit = QUERY_LIMIT, offset = 0 }, callback) => {
    const body = JSON.stringify({
        ViewInput: {
            identifier: `udw-load-content-info-${contentId}`,
            public: false,
            ContentQuery: {
                FacetBuilders: {},
                SortClauses: {},
                Filter: { ContentIdCriterion: `${contentId}` },
                limit,
                offset,
            },
        },
    });
    const request = new Request(ENDPOINT_CREATE_VIEW, {
        method: 'POST',
        headers: { ...HEADERS_CREATE_VIEW, 'X-Siteaccess': siteaccess, 'X-CSRF-Token': token },
        body,
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then((response) => {
            const items = response.View.Result.searchHits.searchHit.map((searchHit) => searchHit.value.Content);

            callback(items);
        })
        .catch(showErrorNotificationAbortWrapper);
};

export const loadBookmarks = ({ token, siteaccess, limit, offset }, callback) => {
    const request = new Request(`${ENDPOINT_BOOKMARK}?limit=${limit}&offset=${offset}`, {
        method: 'GET',
        headers: {
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
            Accept: 'application/vnd.ez.api.ContentTypeInfoList+json',
        },
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then((response) => {
            const count = response.BookmarkList.count;
            const items = response.BookmarkList.items.map((item) => item.Location);

            callback({ count, items });
        })
        .catch(showErrorNotificationAbortWrapper);
};

const toggleBookmark = ({ siteaccess, token, locationId }, callback, method) => {
    const request = new Request(`${ENDPOINT_BOOKMARK}/${locationId}`, {
        method,
        headers: {
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
        },
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponseStatus)
        .then(callback)
        .catch(showErrorNotificationAbortWrapper);
};

export const addBookmark = (options, callback) => {
    toggleBookmark(options, callback, 'POST');
};

export const removeBookmark = (options, callback) => {
    toggleBookmark(options, callback, 'DELETE');
};

export const loadContentTypes = ({ token, siteaccess }, callback) => {
    const request = new Request('/api/ezp/v2/content/types', {
        method: 'GET',
        headers: {
            Accept: 'application/vnd.ez.api.ContentTypeInfoList+json',
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
        },
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then(callback)
        .catch(showErrorNotificationAbortWrapper);
};

export const createDraft = ({ token, siteaccess, contentId }, callback) => {
    const request = new Request(`/api/ezp/v2/content/objects/${contentId}/currentversion`, {
        method: 'COPY',
        headers: {
            Accept: 'application/vnd.ez.api.VersionUpdate+json',
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
        },
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then(callback)
        .catch(showErrorNotificationAbortWrapper);
};

export const loadContentInfo = ({ token, siteaccess, contentId, limit = QUERY_LIMIT, offset = 0, signal }, callback) => {
    const body = JSON.stringify({
        ViewInput: {
            identifier: `udw-load-content-info-${contentId}`,
            public: false,
            ContentQuery: {
                FacetBuilders: {},
                SortClauses: {},
                Filter: { ContentIdCriterion: `${contentId}` },
                limit,
                offset,
            },
        },
    });
    const request = new Request(ENDPOINT_CREATE_VIEW, {
        method: 'POST',
        headers: Object.assign({}, HEADERS_CREATE_VIEW, {
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
        }),
        body,
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request, { signal })
        .then(handleRequestResponse)
        .then((response) => {
            const items = response.View.Result.searchHits.searchHit.map((searchHit) => searchHit.value.Content);

            callback(items);
        })
        .catch(showErrorNotificationAbortWrapper);
};

export const loadLocationsWithPermissions = ({ locationIds, signal }, callback) => {
    const url = window.Routing.generate('ezplatform.udw.locations.data');
    const request = new Request(`${url}?locationIds=${locationIds}`, {
        method: 'GET',
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request, { signal })
        .then(handleRequestResponse)
        .then(callback)
        .catch(showErrorNotificationAbortWrapper);
};
