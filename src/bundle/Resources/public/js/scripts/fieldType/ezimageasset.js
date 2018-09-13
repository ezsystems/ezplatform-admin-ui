(function(global, doc, eZ, React, ReactDOM, Translator) {
    const SELECTOR_FIELD = '.ez-field-edit--ezimageasset';
    const SELECTOR_INPUT_FILE = 'input[type="file"]';
    const SELECTOR_INPUT_DESTINATION_CONTENT_ID = '.ez-data-source__destination-content-id';
    const SELECTOR_LABEL_WRAPPER = '.ez-field-edit__label-wrapper';
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const showErrorNotification = eZ.helpers.notification.showErrorNotification;
    const showSuccessNotification = eZ.helpers.notification.showSuccessNotification;
    const getJsonFromResponse = eZ.helpers.request.getJsonFromResponse;
    const imageAssetMapping = eZ.adminUiConfig.imageAssetMapping;

    /**
     * Handles response status
     *
     * @function handleResponseStatus
     * @param {Response} response
     * @returns {Error|Promise}
     */
    const handleResponseStatus = (response) => {
        if (response.status === 'failed') {
            throw new Error(response.error);
        }

        return response;
    };

    class EzImageAssetPreviewField extends eZ.BasePreviewField {
        /**
         * Creates a new Image Asset
         *
         * @method createAsset
         * @param {File} file
         * @param {String} languageCode
         */
        createAsset(file, languageCode) {
            const assetCreateUri = global.Routing.generate('ezplatform.asset.upload_image');
            const form = new FormData();

            form.append('languageCode', languageCode);
            form.append('file', file);

            const options = {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-Token': token,
                },
                body: form,
                mode: 'same-origin',
                credentials: 'same-origin',
            };

            this.toggleLoading(true);

            fetch(assetCreateUri, options)
                .then(getJsonFromResponse)
                .then(handleResponseStatus)
                .then(this.onAssetCreateSuccess.bind(this))
                .catch(this.onAssetCreateFailure.bind(this));
        }

        /**
         * Handle a successfully created Image Asset
         *
         * @method onAssetCreateSuccess
         * @param {Object} response
         */
        onAssetCreateSuccess(response) {
            const destinationContent = response.destinationContent;

            this.updateData(destinationContent.id, destinationContent.name, destinationContent.locationId, response.value);
            this.toggleLoading(false);

            showSuccessNotification(
                Translator.trans(
                    /* @Desc("Image has been published and can now be reused") */ 'ezimageasset.create.message.success',
                    {},
                    'fieldtypes_edit'
                )
            );
        }

        /**
         * Handle a failure while creating Image Asset
         *
         * @method onAssetCreateFailure
         */
        onAssetCreateFailure(error) {
            const message = Translator.trans(
                /* @Desc("Error while creating image asset: %error%") */ 'ezimageasset.create.message.error',
                { error: error.message },
                'fieldtypes_edit'
            );

            this.toggleLoading(false);
            showErrorNotification(message);
        }

        /**
         * Loads selected Image Asset
         *
         * @method loadAsset
         * @param {Object} response
         */
        loadAsset(response) {
            const imageField = response.CurrentVersion.Version.Fields.field.find((field) => {
                return field.fieldDefinitionIdentifier === imageAssetMapping['contentFieldIdentifier'];
            });

            this.updateData(response.ContentInfo.Content._id, response.ContentInfo.Content.Name, response.id, imageField.fieldValue);
        }

        /**
         * Toggle visibility of the loading spinner
         *
         * @method toggleLoading
         * @param {boolean} show
         */
        toggleLoading(show) {
            this.fieldContainer.classList.toggle('ez-field-edit--is-preview-loading', show);
        }

        /**
         * Updates Image Asset preview data
         *
         * @method updateData
         * @param {Number} destinationContentId
         * @param {String} destinationContentName
         * @param {Number} destinationLocationId
         * @param {Object} image
         */
        updateData(destinationContentId, destinationContentName, destinationLocationId, image) {
            const preview = this.fieldContainer.querySelector('.ez-field-edit__preview');
            const previewImg = preview.querySelector('.ez-field-edit-preview__media');
            const previewAlt = preview.querySelector('.ez-field-edit-preview__image-alt input');
            const previewActionPreview = preview.querySelector('.ez-field-edit-preview__action--preview');
            const assetNameContainer = preview.querySelector('.ez-field-edit-preview__asset-name a');
            const destinationLocationUrl = global.Routing.generate('_ezpublishLocation', {
                locationId: destinationLocationId
            });

            previewImg.setAttribute('src', image ? image.uri : '://0');
            previewImg.classList.toggle('d-none', image === null);
            previewAlt.value = image.alternativeText;
            previewActionPreview.setAttribute('href', destinationLocationUrl);
            assetNameContainer.innerHTML = destinationContentName;
            assetNameContainer.setAttribute('href', destinationLocationUrl);

            this.inputDestinationContentId.value = destinationContentId;
            this.inputField.value = '';
            this.showPreview();
        }

        /**
         * Open UDW to select an existing Image Asset
         *
         * @method openUDW
         * @param {Event} event
         */
        openUDW(event) {
            event.preventDefault();

            const udwContainer = doc.getElementById('react-udw');
            const config = JSON.parse(event.currentTarget.dataset.udwConfig);
            const title = Translator.trans(/*@Desc("Select Image Asset")*/ 'ezimageasset.title', {}, 'universal_discovery_widget');
            const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
            const onCancel = closeUDW;
            const onConfirm = (items) => {
                closeUDW();
                this.loadAsset(items[0]);
            };
            const canSelectContent = ({ item }, callback) => {
                const itemContentType = item.ContentInfo.Content.ContentTypeInfo.identifier;
                const isAllowedContentType = itemContentType === imageAssetMapping['contentTypeIdentifier'];

                callback(isAllowedContentType);
            };

            ReactDOM.render(
                React.createElement(
                    eZ.modules.UniversalDiscovery,
                    Object.assign(
                        {
                            onConfirm,
                            onCancel,
                            canSelectContent,
                            title,
                            restInfo: { token, siteaccess },
                        },
                        config
                    )
                ),
                udwContainer
            );
        }

        /**
         * Checks if file size is an allowed limit
         *
         * @method handleInputChange
         * @param {Event} event
         */
        handleInputChange(event) {
            const file = event.currentTarget.files[0];
            const languageCode = event.currentTarget.dataset.languageCode;
            const isFileSizeLimited = this.maxFileSize > 0;
            const maxFileSizeExceeded = isFileSizeLimited && file.size > this.maxFileSize;

            if (maxFileSizeExceeded) {
                this.resetInputField();
                return;
            }

            this.fieldContainer.querySelector('.ez-field-edit__option--remove-media').checked = false;

            this.createAsset(file, languageCode);
        }

        /**
         * Resets input field state
         *
         * @method resetInputField
         */
        resetInputField() {
            super.resetInputField();

            this.inputDestinationContentId.value = '';
        }

        /**
         * Initializes the preview
         *
         * @method init
         */
        init() {
            super.init();

            this.btnSelect = this.fieldContainer.querySelector('.ez-data-source__btn-select');
            this.btnSelect.addEventListener('click', this.openUDW.bind(this), false);
            this.inputDestinationContentId = this.fieldContainer.querySelector(SELECTOR_INPUT_DESTINATION_CONTENT_ID);
        }
    }

    class EzImageAssetFieldValidator extends eZ.BaseFileFieldValidator {}

    doc.querySelectorAll(SELECTOR_FIELD).forEach((fieldContainer) => {
        const validator = new EzImageAssetFieldValidator({
            classInvalid: 'is-invalid',
            fieldContainer,
            eventsMap: [
                {
                    selector: `${SELECTOR_INPUT_FILE}`,
                    eventName: 'change',
                    callback: 'validateInput',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                },
                {
                    isValueValidator: false,
                    selector: `${SELECTOR_INPUT_FILE}`,
                    eventName: 'invalidFileSize',
                    callback: 'showFileSizeError',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                },
            ],
        });

        const previewField = new EzImageAssetPreviewField({
            validator,
            fieldContainer,
            fileTypeAccept: fieldContainer.querySelector(SELECTOR_INPUT_FILE).accept,
        });

        previewField.init();

        eZ.fieldTypeValidators = eZ.fieldTypeValidators ? [...eZ.fieldTypeValidators, validator] : [validator];
    });
})(window, document, window.eZ, window.React, window.ReactDOM, window.Translator);
