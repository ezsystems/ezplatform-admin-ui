import React, { Component } from 'react';
import PropTypes from 'prop-types';

import UploadItemComponent from './upload.item.component';

export default class UploadListComponent extends Component {
    constructor(props) {
        super(props);

        this.state = {
            itemsToUpload: props.itemsToUpload,
            items: [],
        };
    }

    UNSAFE_componentWillReceiveProps(props) {
        this.setState((state) => {
            const stateItems = state.itemsToUpload.filter(
                (stateItem) => !props.itemsToUpload.find((propItem) => propItem.id === stateItem.id)
            );

            return { itemsToUpload: [...stateItems, ...props.itemsToUpload] };
        });
    }

    componentDidUpdate() {
        this.props.onAfterUpload(this.state.items);
    }

    /**
     * Handles after file upload event
     *
     * @method handleAfterUpload
     * @param {Object} item
     * @memberof UploadListComponent
     */
    handleAfterUpload(item) {
        this.setState((state) => ({
            itemsToUpload: state.itemsToUpload.filter((data) => data.id !== item.id),
            items: [...state.items, item],
        }));
    }

    /**
     * Handles after file upload abort event
     *
     * @method handleAfterAbort
     * @param {Object} item
     * @memberof UploadListComponent
     */
    handleAfterAbort(item) {
        this.setState((state) => {
            const items = state.items.filter((data) => data.id !== item.id);
            const itemsToUpload = state.itemsToUpload.filter((data) => data.id !== item.id);

            return Object.assign({}, state, {
                uploaded: items.length,
                total: items.length + itemsToUpload.length,
                itemsToUpload,
                items,
            });
        });
    }

    /**
     * Handles after file delete event
     *
     * @method handleAfterDelete
     * @param {Object} item
     * @memberof UploadListComponent
     */
    handleAfterDelete(item) {
        this.setState((state) => {
            const items = state.items.filter((data) => data.id !== item.id);
            const itemsToUpload = state.itemsToUpload.filter((data) => data.id !== item.id);

            return Object.assign({}, state, {
                uploaded: items.length,
                total: items.length + itemsToUpload.length,
                itemsToUpload,
                items,
            });
        });
    }

    /**
     * Renders an item to upload
     *
     * @method renderItemToUpload
     * @param {Object} item
     * @memberof UploadListComponent
     * @returns {Element}
     */
    renderItemToUpload(item) {
        return this.renderItem(item, {
            isUploaded: false,
            createFileStruct: this.props.createFileStruct,
            publishFile: this.props.publishFile,
            onAfterAbort: this.handleAfterAbort.bind(this),
            onAfterUpload: this.handleAfterUpload.bind(this),
            checkCanUpload: this.props.checkCanUpload,
        });
    }

    /**
     * Renders an uploaded item
     *
     * @method renderUploadedItem
     * @param {Object} item
     * @memberof UploadListComponent
     * @returns {Element}
     */
    renderUploadedItem(item) {
        return this.renderItem(item, {
            isUploaded: true,
            deleteFile: this.props.deleteFile,
            onAfterDelete: this.handleAfterDelete.bind(this),
        });
    }

    /**
     * Renders an item
     *
     * @method renderItem
     * @param {Object} item
     * @param {Object} customAttrs component's custom attrs
     * @memberof UploadListComponent
     * @returns {Element}
     */
    renderItem(item, customAttrs) {
        const { adminUiConfig, parentInfo, contentCreatePermissionsConfig, contentTypesMap, currentLanguage } = this.props;
        const attrs = Object.assign(
            {
                key: item.id,
                data: item,
                adminUiConfig,
                parentInfo,
                contentCreatePermissionsConfig,
                contentTypesMap,
                currentLanguage,
            },
            customAttrs
        );

        return <UploadItemComponent {...attrs} />;
    }

    render() {
        const { items, itemsToUpload } = this.state;
        const uploaded = items.length;
        const total = uploaded + itemsToUpload.length;

        return (
            <div className="c-upload-list">
                <div className="c-upload-list__title">
                    {this.props.uploadedItemsListTitle} ({uploaded}/{total})
                </div>
                <div className="c-upload-list__items">
                    {itemsToUpload.map(this.renderItemToUpload.bind(this))}
                    {items.map(this.renderUploadedItem.bind(this))}
                </div>
            </div>
        );
    }
}

UploadListComponent.propTypes = {
    itemsToUpload: PropTypes.arrayOf(PropTypes.object),
    onAfterUpload: PropTypes.func.isRequired,
    createFileStruct: PropTypes.func.isRequired,
    publishFile: PropTypes.func.isRequired,
    deleteFile: PropTypes.func.isRequired,
    checkCanUpload: PropTypes.func.isRequired,
    adminUiConfig: PropTypes.shape({
        multiFileUpload: PropTypes.shape({
            defaultMappings: PropTypes.arrayOf(PropTypes.object).isRequired,
            fallbackContentType: PropTypes.object.isRequired,
            locationMappings: PropTypes.arrayOf(PropTypes.object).isRequired,
            maxFileSize: PropTypes.number.isRequired,
        }).isRequired,
        token: PropTypes.string.isRequired,
        siteaccess: PropTypes.string.isRequired,
    }).isRequired,
    parentInfo: PropTypes.shape({
        contentTypeIdentifier: PropTypes.string.isRequired,
        contentTypeId: PropTypes.number.isRequired,
        locationPath: PropTypes.string.isRequired,
        language: PropTypes.string.isRequired,
    }).isRequired,
    uploadedItemsListTitle: PropTypes.string.isRequired,
    contentCreatePermissionsConfig: PropTypes.object.isRequired,
    contentTypesMap: PropTypes.object.isRequired,
    currentLanguage: PropTypes.string,
};

UploadListComponent.defaultProps = {
    itemsToUpload: [],
    currentLanguage: '',
};
