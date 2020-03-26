import { handleRequestResponse, ENDPOINT_CONTENT_TYPES } from './common.service';

export const loadContentTypes = (contentTypeIds, callback) => {
    const request = new Request(ENDPOINT_CONTENT_TYPES, {
        method: 'GET',
        headers: { Accept: 'application/vnd.ez.api.ContentTypeInfoList+json' },
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then(callback)
        .catch(() => window.eZ.helpers.notification.showErrorNotification('Cannot load content types'));
};
