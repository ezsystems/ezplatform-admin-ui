export const ENDPOINT_VIEWS = '/api/ezp/v2/views';
export const ENDPOINT_CONTENT_TYPES = '/api/ezp/v2/content/types';
export const HEADERS_VIEWS = {
    Accept: 'application/vnd.ez.api.View+json; version=1.1',
    'Content-Type': 'application/vnd.ez.api.ViewInput+json; version=1.1',
};

export const handleRequestResponse = (response) => {
    if (!response.ok) {
        throw Error(response.statusText);
    }

    return response.json();
};
