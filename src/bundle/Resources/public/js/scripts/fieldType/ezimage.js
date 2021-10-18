(function(global, doc, eZ, Translator) {
    const SELECTOR_FIELD = '.ibexa-field-edit--ezimage';
    const SELECTOR_INPUT_FILE = 'input[type="file"]';
    const SELECTOR_FILESIZE_NOTICE = '.ibexa-data-source__message--filesize';
    const SELECTOR_ALT_WRAPPER = '.ibexa-field-edit-preview__image-alt';
    const SELECTOR_INPUT_ALT = '.ibexa-field-edit-preview__image-alt .ibexa-data-source__input';
    const EVENT_CANCEL_ERROR = 'ibexa-cancel-errors';

    class EzImageFilePreviewField extends eZ.BasePreviewField {
        /**
         * Gets a temporary image URL
         *
         * @method getImageUrl
         * @param {File} file
         * @param {Function} callback the callback returns a retrieved file's temporary URL
         */
        getImageUrl(file, callback) {
            const reader = new FileReader();

            reader.onload = (event) => callback(event.target.result);
            reader.readAsDataURL(file);
        }

        /**
         * Loads dropped file preview.
         * It should redefined in each class that extends this one.
         *
         * @method loadDroppedFilePreview
         * @param {Event} event
         */
        loadDroppedFilePreview(event) {
            const preview = this.fieldContainer.querySelector('.ibexa-field-edit__preview');
            const imageNode = preview.querySelector('.ibexa-field-edit-preview__media');
            const nameContainer = preview.querySelector('.ibexa-field-edit-preview__file-name');
            const sizeContainer = preview.querySelector('.ibexa-field-edit-preview__file-size');
            const files = [].slice.call(event.target.files);
            const fileSize = this.formatFileSize(files[0].size);

            this.getImageUrl(files[0], (url) => {
                var image = new Image();

                image.onload = function() {
                    const width = image.width;
                    const height = image.height;
                    const widthNode = preview.querySelector('.ibexa-field-edit-preview__dimension--width');
                    const heightNode = preview.querySelector('.ibexa-field-edit-preview__dimension--height');

                    widthNode.innerHTML = Translator.trans(
                        /* @Desc("W:%width% px") */ 'ezimage.dimensions.width',
                        { width },
                        'fieldtypes_edit'
                    );
                    heightNode.innerHTML = Translator.trans(
                        /* @Desc("H:%height% px") */ 'ezimage.dimensions.height',
                        { height },
                        'fieldtypes_edit'
                    );
                };

                image.src = url;
                imageNode.setAttribute('src', url);
            });

            nameContainer.innerHTML = files[0].name;
            nameContainer.title = files[0].name;
            sizeContainer.innerHTML = fileSize;
            sizeContainer.title = fileSize;

            preview.querySelector('.ibexa-field-edit-preview__action--preview').href = URL.createObjectURL(files[0]);
            this.fieldContainer.querySelector(SELECTOR_INPUT_ALT).dispatchEvent(new CustomEvent(EVENT_CANCEL_ERROR));
        }
    }

    class EzImageFieldValidator extends eZ.BaseFileFieldValidator {
        toggleInvalidState(isError, config, input) {
            super.toggleInvalidState(isError, config, input);

            const container = this.getFieldTypeContainer(input.closest(this.fieldSelector));
            const method = !!container.querySelector(`.${this.classInvalid}`) ? 'add' : 'remove';

            container.classList[method](this.classInvalid);
        }

        validateFileSize(event) {
            event.currentTarget.dispatchEvent(new CustomEvent('ibexa-invalid-file-size'));

            return {
                isError: false,
            };
        }

        /**
         * Validates the alternative text input
         *
         * @method validateAltInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzStringValidator
         */
        validateAltInput(event) {
            const fileField = this.fieldContainer.querySelector(SELECTOR_INPUT_FILE);
            const dataContainer = this.fieldContainer.querySelector('.ibexa-field-edit__data');
            const isFileFieldEmpty = fileField.files && !fileField.files.length && dataContainer && !dataContainer.hasAttribute('hidden');
            const isRequired = event.target.dataset.isRequired;
            const isEmpty = !event.target.value;
            const isError = isEmpty && isRequired && !isFileFieldEmpty;
            const label = event.target.closest(SELECTOR_ALT_WRAPPER).querySelector('.ibexa-data-source__label').innerHTML;
            const result = { isError };

            if (isEmpty) {
                result.errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);
            }

            return result;
        }
    }

    doc.querySelectorAll(SELECTOR_FIELD).forEach((fieldContainer) => {
        const validator = new EzImageFieldValidator({
            classInvalid: 'is-invalid',
            fieldContainer,
            eventsMap: [
                {
                    selector: `${SELECTOR_INPUT_FILE}`,
                    eventName: 'change',
                    callback: 'validateInput',
                    errorNodeSelectors: ['.ibexa-form-error'],
                },
                {
                    selector: SELECTOR_INPUT_ALT,
                    eventName: 'blur',
                    callback: 'validateAltInput',
                    invalidStateSelectors: ['.ibexa-data-source__field--alternativeText'],
                    errorNodeSelectors: [`${SELECTOR_ALT_WRAPPER} .ibexa-data-source__label-wrapper`],
                },
                {
                    isValueValidator: false,
                    selector: `${SELECTOR_INPUT_FILE}`,
                    eventName: 'ibexa-invalid-file-size',
                    callback: 'showFileSizeError',
                    errorNodeSelectors: [SELECTOR_FILESIZE_NOTICE],
                },
                {
                    isValueValidator: false,
                    selector: SELECTOR_INPUT_ALT,
                    eventName: EVENT_CANCEL_ERROR,
                    callback: 'cancelErrors',
                    invalidStateSelectors: ['.ibexa-data-source__field--alternativeText'],
                    errorNodeSelectors: [`${SELECTOR_ALT_WRAPPER} .ibexa-data-source__label-wrapper`],
                },
            ],
        });
        const previewField = new EzImageFilePreviewField({
            validator,
            fieldContainer,
            fileTypeAccept: fieldContainer.querySelector(SELECTOR_INPUT_FILE).accept,
        });

        previewField.init();

        eZ.addConfig('fieldTypeValidators', [validator], true);
    });
})(window, window.document, window.eZ, window.Translator);
