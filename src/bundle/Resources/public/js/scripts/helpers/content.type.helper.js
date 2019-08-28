(function(global, doc, eZ) {
    let contentTypesDataMap = null;

    /**
     * Creates map with content types identifiers as keys for faster lookup
     *
     * @function createContentTypeDataMap
     * @returns {Object} contentTypesDataMap
     */
    const createContentTypeDataMap = () =>
        Object.values(eZ.adminUiConfig.contentTypes).reduce((contentTypeDataMap, contentTypeGroup) => {
            for (const contentTypeData of contentTypeGroup) {
                contentTypeDataMap[contentTypeData.identifier] = contentTypeData;
            }

            return contentTypeDataMap;
        }, {});

    /**
     * Returns an URL to a content type icon
     *
     * @function getContentTypeIcon
     * @param {String} contentTypeIdentifier
     * @returns {String|null} url to icon
     */
    const getContentTypeIconUrl = (contentTypeIdentifier) => {
        if (!contentTypesDataMap) {
            contentTypesDataMap = createContentTypeDataMap();
        }

        if (!contentTypeIdentifier || !contentTypesDataMap[contentTypeIdentifier]) {
            return null;
        }

        const iconUrl = contentTypesDataMap[contentTypeIdentifier].thumbnail;

        return iconUrl;
    };

    /**
     * Returns contentType name from contentType identifier
     *
     * @function getContentTypeName
     * @param {String} contentTypeIdentifier
     * @returns {String|null} contentType name
     */
    const getContentTypeName = (contentTypeIdentifier) => {
        let contentType;
        for (var sectionContentTypes of Object.values(eZ.adminUiConfig.contentTypes)) {
            contentType = sectionContentTypes.find(({ identifier }) => identifier === contentTypeIdentifier);
            if (contentType) {
                break;
            }
        }
        return contentType.name || null;
    };

    eZ.addConfig('helpers.contentType', {
        getContentTypeIconUrl,
        getContentTypeName
    });
})(window, window.document, window.eZ);
