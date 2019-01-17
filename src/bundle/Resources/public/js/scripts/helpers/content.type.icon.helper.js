(function(global, doc, eZ) {
    let contentTypesDataMap = null;

    /**
     * Creates map with content types identifiers as keys for faster lookup
     *
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
     * Returns href to content type icon
     *
     * @function showNotification
     * @param {String} contentTypeIdentifier
     * @returns {String|null} href to icon
     */
    const getContentTypeIcon = (contentTypeIdentifier) => {
        if (!contentTypeIdentifier) {
            null;
        }

        if (!contentTypesDataMap) {
            contentTypesDataMap = createContentTypeDataMap();
        }

        const iconHref = contentTypesDataMap[contentTypeIdentifier].thumbnail;

        return iconHref;
    };

    eZ.addConfig('helpers.contentType', {
        getContentTypeIcon,
    });
})(window, document, window.eZ);
