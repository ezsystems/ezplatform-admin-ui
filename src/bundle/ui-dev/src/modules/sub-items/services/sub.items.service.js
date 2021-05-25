import { handleRequestResponse } from '../../common/helpers/request.helper.js';
import { ASCENDING_SORT_ORDER } from '../sub.items.module.js';
import { LOCATION_ENDPOINT, ENDPOINT_GRAPHQL } from './endpoints.js';

const sortClauseGraphQLMap = {
    ContentId: '_contentId',
    ContentName: '_name',
    DateModified: '_dateModified',
    DatePublished: '_datePublished',
    LocationDepth: '_depth',
    LocationPath: '_path',
    LocationPriority: '_priority',
    SectionIdentifier: '_sectionIdentifier',
    SectionName: '_sectionName',
};

/**
 * Loads location's children
 *
 * @function loadLocation
 * @param {Object} restInfo - contains:
 * @param {String} restInfo.token
 * @param {String} restInfo.siteaccess
 * @param {Object} queryConfig - contains:
 * @param {Number} queryConfig.locationId
 * @param {Number} queryConfig.limit
 * @param {Number} queryConfig.cursor
 * @param {Object} queryConfig.sortClause
 * @param {Object} queryConfig.sortOrder
 * @param {Function} callback
 */
export const loadLocation = ({ token, siteaccess }, { locationId = 2, limit = 10, cursor, sortClause, sortOrder }, callback) => {
    const queryAfter = cursor ? `, after: "${cursor}"` : '';
    const querySortClause = sortClauseGraphQLMap[sortClause];
    const querySortOrder = sortOrder === ASCENDING_SORT_ORDER ? '' : '_desc';
    const querySortBy = querySortClause ? `sortBy: [${querySortClause}, ${querySortOrder}], ` : '';
    const query = `
    {
        _repository {
            location(locationId: ${locationId}) {
                pathString
                children(${querySortBy} first:${limit} ${queryAfter}) {
                    totalCount
                    pages {
                        number
                        cursor
                    }
                    edges {
                        node {
                            id
                            remoteId
                            invisible
                            hidden
                            priority
                            pathString
    
                            content {
                                _thumbnail {
                                    uri
                                    alternativeText
                                    mimeType
                                }
                                _name
                                _info {
                                    id
                                    name
                                    remoteId
                                    mainLanguageCode
                                    owner {
                                        name
                                    }
                                    currentVersion {
                                        versionNumber
                                        creator {
                                            name
                                        }
                                        languageCodes
                                    }
                                    contentType {
                                        name
                                        identifier
                                    }
                                    section {
                                        name
                                    }
                                    publishedDate {
                                        timestamp
                                    }
                                    modificationDate {
                                        timestamp
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }`;

    const request = new Request(ENDPOINT_GRAPHQL, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
        },
        body: JSON.stringify({
            query,
        }),
        mode: 'cors',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then(callback)
        .catch(() => window.eZ.helpers.notification.showErrorNotification('Cannot load location'));
};

/**
 * Updates location priority
 *
 * @function updateLocationPriority
 * @param {Object} params params hash containing: priority, location, token, siteaccess properties
 * @param {Function} callback
 */
export const updateLocationPriority = ({ priority, pathString, token, siteaccess }, callback) => {
    const locationHref = `${LOCATION_ENDPOINT}${pathString.slice(0, -1)}`;

    const request = new Request(locationHref, {
        method: 'POST',
        headers: {
            Accept: 'application/vnd.ez.api.Location+json',
            'Content-Type': 'application/vnd.ez.api.LocationUpdate+json',
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
            'X-HTTP-Method-Override': 'PATCH',
        },
        credentials: 'same-origin',
        mode: 'cors',
        body: JSON.stringify({
            LocationUpdate: {
                priority: priority,
            },
        }),
    });

    fetch(request)
        .then(handleRequestResponse)
        .then(callback)
        .catch(() => window.eZ.helpers.notification.showErrorNotification('An error occurred while updating location priority'));
};
