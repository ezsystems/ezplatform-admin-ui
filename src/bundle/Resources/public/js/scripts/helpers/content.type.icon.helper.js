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
     * Returns href to content type icon
     *
     * @function getContentTypeIcon
     * @param {String} contentTypeIdentifier
     * @returns {String|null} href to icon
     */
    const getContentTypeIcon = (contentTypeIdentifier) => {
        if (!contentTypesDataMap) {
            contentTypesDataMap = createContentTypeDataMap();
        }

        if (!contentTypeIdentifier || !contentTypesDataMap[contentTypeIdentifier]) {
            return null;
        }

        const iconHref = contentTypesDataMap[contentTypeIdentifier].thumbnail;

        return iconHref;
    };

    eZ.addConfig('helpers.contentType', {
        getContentTypeIcon,
    });
})(window, document, window.eZ);
