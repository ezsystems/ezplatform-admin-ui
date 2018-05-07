import React, { Component } from 'react';
import PropTypes from 'prop-types';
import EzEmbedDiscoverContentButton from './ez-embeddiscovercontent';

export default class EzEmbedImageButton extends EzEmbedDiscoverContentButton {
    /**
     * Checks whether the current selection can be considered as an image.
     * This is the case if the content type has an ezimage field definition
     * and if the corresponding field is not empty. This method is meant to
     * be used as a `isSelectable` function implementation for the UDW.
     *
     * @method isImage
     * @param {Object} selection the UDW potential selection
     * @param {Function} callback
     */
    isImage(selection, callback) {
        console.warn('[DEPRECATED] isImage method is deprecated');
        console.warn('[DEPRECATED] it will be removed from ezplatform-admin-ui 2.0');
        console.warn('[DEPRECATED] please use richtext_embed_image.content_on_the_fly.allowed_content_types in the UDW config');

        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
        const request = new Request(selection.item.ContentInfo.Content.ContentType._href, {
            method: 'GET',
            headers: {
                'Accept': 'application/vnd.ez.api.ContentType+json',
                'X-Siteaccess': siteaccess
            },
            mode: 'same-origin',
            credentials: 'same-origin'
        });

        fetch(request)
            .then(response => response.json())
            .then(data => callback(!!data.ContentType.FieldDefinitions.FieldDefinition.find(field => field.fieldType === 'ezimage')));
    }
}
