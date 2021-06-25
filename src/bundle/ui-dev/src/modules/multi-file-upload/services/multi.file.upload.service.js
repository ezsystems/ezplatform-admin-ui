/**
 * Handles ready state change of request
 *
 * @function handleOnReadyStateChange
 * @param {XMLHttpRequest} xhr
 * @param {Function} onSuccess
 * @param {Function} onError
 */
const handleOnReadyStateChange = (xhr, onSuccess, onError) => {
    if (xhr.readyState !== 4) {
        return;
    }

    if (xhr.status === 0 && xhr.statusText === '') {
        // request aborted
        return;
    }

    if (xhr.status >= 400 || !xhr.status) {
        onError(xhr);

        return;
    }

    onSuccess(JSON.parse(xhr.response));
};

/**
 * Handles request response
 *
 * @function handleRequestResponse
 * @param {Response} response
 * @returns {String|Response}
 */
const handleRequestResponse = (response) => {
    if (!response.ok) {
        throw Error(response.text());
    }

    return response;
};

/**
 * Read file handler
 *
 * @function readFile
 * @param {File} file
 * @param {Function} resolve
 * @param {Function} reject
 */
const readFile = function(file, resolve, reject) {
    this.addEventListener('load', () => resolve({ fileReader: this, file }), false);
    this.addEventListener('error', () => reject(), false);
    this.readAsDataURL(file);
};

/**
 * Finds a content type mapping based on a file type
 *
 * @function findFileTypeMapping
 * @param {Array} mappings
 * @param {File} file
 * @returns {Object|undefined}
 */
const findFileTypeMapping = (mappings, file) => mappings.find((item) => item.mimeTypes.find((type) => type === file.type));

/**
 * Checks if file's MIME Type is allowed
 *
 * @function isMimeTypeAllowed
 * @param {Array} mappings
 * @param {File} file
 * @returns {Boolean}
 */
const isMimeTypeAllowed = (mappings, file) => !!findFileTypeMapping(mappings, file);

/**
 * Checks if file type is allowed
 *
 * @function checkFileTypeAllowed
 * @param {File} file
 * @param {Object} locationMapping
 * @returns {Boolean}
 */
const checkFileTypeAllowed = (file, locationMapping) => (!locationMapping ? true : isMimeTypeAllowed(locationMapping.mappings, file));

/**
 * Detects a content type for a given file
 *
 * @function detectContentTypeMapping
 * @param {File} file
 * @param {Object} parentInfo
 * @param {Object} config
 * @returns {Object} detected content type config
 */
const detectContentTypeMapping = (file, parentInfo, config) => {
    const locationMapping = config.locationMappings.find((item) => item.contentTypeIdentifier === parentInfo.contentTypeIdentifier);
    const mappings = locationMapping ? locationMapping.mappings : config.defaultMappings;

    return findFileTypeMapping(mappings, file) || config.fallbackContentType;
};

/**
 * Gets content type identifier
 *
 * @function getContentTypeByIdentifier
 * @param {Object} params params object containing token and siteaccess properties
 * @param {String} identifier content type identifier
 * @returns {Promise}
 */
const getContentTypeByIdentifier = ({ token, siteaccess }, identifier) => {
    const request = new Request(`/api/ezp/v2/content/types?identifier=${identifier}`, {
        method: 'GET',
        headers: {
            Accept: 'application/vnd.ez.api.ContentTypeInfoList+json',
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
        },
        credentials: 'same-origin',
        mode: 'cors',
    });

    return fetch(request).then(handleRequestResponse);
};

/**
 * Prepares a ContentCreate struct based on an uploaded file type
 *
 * @function prepareStruct
 * @param {Object} params params object containing parentInfo and config properties
 * @param {Object} data file data containing File object and FileReader object
 * @returns {Promise}
 */
const prepareStruct = ({ parentInfo, config, languageCode }, data) => {
    let parentLocation = `/api/ezp/v2/content/locations${parentInfo.locationPath}`;

    parentLocation = parentLocation.endsWith('/') ? parentLocation.slice(0, -1) : parentLocation;

    const mapping = detectContentTypeMapping(data.file, parentInfo, config.multiFileUpload);

    return getContentTypeByIdentifier(config, mapping.contentTypeIdentifier)
        .then((response) => response.json())
        .catch(() => window.eZ.helpers.notification.showErrorNotification('Cannot get content type by identifier'))
        .then((response) => {
            const fileValue = {
                fileName: data.file.name,
                data: data.fileReader.result.replace(/^.*;base64,/, ''),
            };

            if (data.file.type.startsWith('image/')) {
                fileValue.alternativeText = data.file.name;
            }

            const fields = [
                { fieldDefinitionIdentifier: mapping.nameFieldIdentifier, fieldValue: data.file.name },
                { fieldDefinitionIdentifier: mapping.contentFieldIdentifier, fieldValue: fileValue },
            ];

            const struct = {
                ContentCreate: {
                    ContentType: { _href: response.ContentTypeInfoList.ContentType[0]._href },
                    mainLanguageCode: languageCode || parentInfo.language,
                    LocationCreate: { ParentLocation: { _href: parentLocation }, sortField: 'PATH', sortOrder: 'ASC' },
                    Section: null,
                    alwaysAvailable: true,
                    remoteId: null,
                    modificationDate: new Date().toISOString(),
                    fields: { field: fields },
                },
            };

            return struct;
        })
        .catch(() => window.eZ.helpers.notification.showErrorNotification('Cannot create content structure'));
};

/**
 * Creates a content draft
 *
 * @function createDraft
 * @param {Object} params params object containing struct, token and siteaccess properties
 * @param {Object} requestEventHandlers object containing a list of callbacks
 * @returns {Promise}
 */
const createDraft = ({ struct, token, siteaccess }, requestEventHandlers) => {
    const xhr = new XMLHttpRequest();
    const body = JSON.stringify(struct);
    const headers = {
        Accept: 'application/vnd.ez.api.Content+json',
        'Content-Type': 'application/vnd.ez.api.ContentCreate+json',
        'X-CSRF-Token': token,
        'X-Siteaccess': siteaccess,
    };

    return new Promise((resolve, reject) => {
        xhr.open('POST', '/api/ezp/v2/content/objects', true);

        xhr.onreadystatechange = handleOnReadyStateChange.bind(null, xhr, resolve, reject);

        if (requestEventHandlers && Object.keys(requestEventHandlers).length) {
            const uploadEvents = requestEventHandlers.upload;

            if (uploadEvents && Object.keys(uploadEvents).length) {
                xhr.upload.onabort = uploadEvents.onabort;
                xhr.upload.onerror = reject;
                xhr.upload.onload = uploadEvents.onload;
                xhr.upload.onprogress = uploadEvents.onprogress;
                xhr.upload.ontimeout = uploadEvents.ontimeout;
            }

            xhr.onerror = reject;
            xhr.onloadstart = requestEventHandlers.onloadstart;
        }

        for (let headerType in headers) {
            if (headers.hasOwnProperty(headerType)) {
                xhr.setRequestHeader(headerType, headers[headerType]);
            }
        }

        xhr.send(body);
    });
};

/**
 * Publishes a content draft
 *
 * @function publishDraft
 * @param {Object} params params object containing token and siteaccess properties
 * @param {Object} response object containing created draft struct
 * @returns {Promise}
 */
const publishDraft = ({ token, siteaccess }, response) => {
    if (!response || !response.hasOwnProperty('Content')) {
        return Promise.reject('Cannot publish content based on an uploaded file');
    }

    const request = new Request(response.Content.CurrentVersion.Version._href, {
        method: 'POST',
        headers: {
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
            'X-HTTP-Method-Override': 'PUBLISH',
        },
        mode: 'cors',
        credentials: 'same-origin',
    });

    return fetch(request).then(handleRequestResponse);
};

/**
 * Checks whether a content based on an uploaded file can be created
 *
 * @function canCreateContent
 * @param {File} file
 * @param {Object} parentInfo parent info hash
 * @param {Object} config multi file upload config
 * @returns {Boolean}
 */
const canCreateContent = (file, parentInfo, config) => {
    if (!config.hasOwnProperty('contentCreatePermissionsConfig') || !config.contentCreatePermissionsConfig) {
        return true;
    }

    const contentTypeConfig = detectContentTypeMapping(file, parentInfo, config);

    return config.contentCreatePermissionsConfig[contentTypeConfig.contentTypeIdentifier];
};

/**
 * Checks if a file can be uploaded
 *
 * @function checkCanUpload
 * @param {File} file
 * @param {Object} parentInfo parent info hash
 * @param {Object} config multi file upload config
 * @param {Object} callbacks a list of callbacks
 * @returns {Boolean}
 */
export const checkCanUpload = (file, parentInfo, config, callbacks) => {
    const locationMapping = config.locationMappings.find((item) => item.contentTypeIdentifier === parentInfo.contentTypeIdentifier);

    if (!canCreateContent(file, parentInfo, config)) {
        callbacks.contentTypeNotAllowedCallback();

        return false;
    }

    if (!checkFileTypeAllowed(file, locationMapping)) {
        callbacks.fileTypeNotAllowedCallback();

        return false;
    }

    if (file.size > config.maxFileSize) {
        callbacks.fileSizeNotAllowedCallback();

        return false;
    }

    return true;
};

/**
 * Creates a ContentCreate struct based on a file
 *
 * @function createFileStruct
 * @param {File} file
 * @param {Object} params struct params
 * @returns {Promise}
 */
export const createFileStruct = (file, params) => new Promise(readFile.bind(new FileReader(), file)).then(prepareStruct.bind(null, params));

/**
 * Publishes file
 *
 * @function publishFile
 * @param {Object} data file data
 * @param {Object} requestEventHandlers a list of request event handlers
 * @param {Function} callback a success callback
 */
export const publishFile = (data, requestEventHandlers, callback) => {
    createDraft(data, requestEventHandlers)
        .then(publishDraft.bind(null, data))
        .then(callback)
        .catch(() => window.eZ.helpers.notification.showErrorNotification('An error occurred while publishing a file'));
};

/**
 * Deletes file
 *
 * @function deleteFile
 * @param {Object} systemInfo system info containing: token and siteaccess info.
 * @param {Object} struct Content struct
 * @param {Function} callback file deleted callback
 */
export const deleteFile = ({ token, siteaccess }, struct, callback) => {
    const request = new Request(struct.Content._href, {
        method: 'DELETE',
        headers: {
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
        },
        mode: 'cors',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then(callback)
        .catch(() => window.eZ.helpers.notification.showErrorNotification('An error occurred while deleting a file'));
};
