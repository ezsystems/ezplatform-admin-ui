import { handleRequestResponse } from '../../common/helpers/request.helper.js';
import {
    TRASH_FAKE_LOCATION,
    USER_ENDPOINT,
    LOCATION_ENDPOINT,
    CONTENT_OBJECTS_ENDPOINT,
    ENDPOINT_BULK,
    HEADERS_BULK,
} from './endpoints.js';

export const bulkMoveLocations = (restInfo, items, newLocationHref, callback) => {
    const requestBodyOperations = {};

    items.forEach(({ id, pathString }) => {
        requestBodyOperations[id] = getBulkMoveRequestOperation(pathString, newLocationHref);
    });

    makeBulkRequest(restInfo, requestBodyOperations, processBulkResponse.bind(null, items, callback));
};

export const bulkAddLocations = (restInfo, items, newLocationHref, callback) => {
    const requestBodyOperations = {};

    items.forEach(({ id, content }) => {
        requestBodyOperations[id] = getBulkAddLocationRequestOperation(content._info.id, newLocationHref);
    });

    makeBulkRequest(restInfo, requestBodyOperations, processBulkResponse.bind(null, items, callback));
};

export const bulkHideLocations = (restInfo, items, callback) => {
    const requestBodyOperations = {};

    items.forEach(({ id, pathString }) => {
        requestBodyOperations[id] = getBulkVisibilityRequestOperation(pathString, true);
    });

    makeBulkRequest(restInfo, requestBodyOperations, processBulkResponse.bind(null, items, callback));
};

export const bulkUnhideLocations = (restInfo, items, callback) => {
    const requestBodyOperations = {};

    items.forEach(({ id, pathString }) => {
        requestBodyOperations[id] = getBulkVisibilityRequestOperation(pathString, false);
    });

    makeBulkRequest(restInfo, requestBodyOperations, processBulkResponse.bind(null, items, callback));
};

export const bulkDeleteItems = (restInfo, items, callback) => {
    const requestBodyOperations = {};

    items.forEach((item) => {
        const { id: locationId, pathString, content } = item;
        const contentTypeIdentifier = content._info.contentType.identifier;
        const isUserContentItem = window.eZ.adminUiConfig.userContentTypes.includes(contentTypeIdentifier);
        const contentId = content._info.id;

        if (isUserContentItem) {
            requestBodyOperations[locationId] = getBulkDeleteUserRequestOperation(contentId);
        } else {
            requestBodyOperations[locationId] = getBulkMoveRequestOperation(pathString, TRASH_FAKE_LOCATION);
        }
    });

    makeBulkRequest(restInfo, requestBodyOperations, processBulkResponse.bind(null, items, callback));
};

const getBulkDeleteUserRequestOperation = (contentId) => ({
    uri: `${USER_ENDPOINT}/${contentId}`,
    method: 'DELETE',
});

const getBulkMoveRequestOperation = (pathString, destination) => ({
    uri: `${LOCATION_ENDPOINT}${pathString.slice(0, -1)}`,
    method: 'MOVE',
    headers: {
        Destination: destination,
    },
});

const getBulkAddLocationRequestOperation = (contentId, destination) => ({
    uri: `${CONTENT_OBJECTS_ENDPOINT}/${contentId}/locations`,
    content: JSON.stringify({
        LocationCreate: {
            ParentLocation: {
                '_href': destination,
            },
            sortField: 'PATH',
            sortOrder: 'ASC',
        },
    }),
    headers: {
        'Content-Type': 'application/vnd.ez.api.LocationCreate+json',
    },
    method: 'POST',
});

const getBulkVisibilityRequestOperation = (pathString, isHidden) => ({
    uri: `${LOCATION_ENDPOINT}${pathString.slice(0, -1)}`,
    content: JSON.stringify({
        LocationUpdate: {
            hidden: isHidden,
            sortField: 'PATH',
            sortOrder: 'ASC',
        },
    }),
    headers: {
        'Content-Type': 'application/vnd.ez.api.LocationUpdate+json',
    },
    method: 'PATCH',
});

const processBulkResponse = (items, callback, response) => {
    const { operations } = response.BulkOperationResponse;
    const itemsMatches = Object.entries(operations).reduce(
        (itemsMatches, [locationId, response]) => {
            const respectiveItem = items.find((item) => item.id === parseInt(locationId, 10));
            const isSuccess = 200 <= response.statusCode && response.statusCode <= 299;

            if (isSuccess) {
                itemsMatches.success.push(respectiveItem);
            } else {
                itemsMatches.fail.push(respectiveItem);
            }

            return itemsMatches;
        },
        { success: [], fail: [] }
    );

    callback(itemsMatches.success, itemsMatches.fail);
};

const makeBulkRequest = ({ token, siteaccess }, requestBodyOperations, callback) => {
    const request = new Request(ENDPOINT_BULK, {
        method: 'POST',
        headers: {
            ...HEADERS_BULK,
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
        },
        body: JSON.stringify({
            bulkOperations: {
                operations: requestBodyOperations,
            },
        }),
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then(callback)
        .catch(() => {
            const message = Translator.trans(
                /*@Desc("An unexpected error occurred while processing the Content item(s). Please try again later.")*/
                'bulk_request.error.message', {}, 'sub_items'
            );

            window.eZ.helpers.notification.showErrorNotification(message);
        });
};
