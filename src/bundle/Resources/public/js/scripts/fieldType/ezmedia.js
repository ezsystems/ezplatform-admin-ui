(function(global, doc, eZ) {
    const SELECTOR_FIELD = '.ibexa-field-edit--ezmedia';
    const SELECTOR_PREVIEW = '.ibexa-field-edit__preview';
    const SELECTOR_MEDIA = '.ibexa-field-edit-preview__media';
    const SELECTOR_INFO_WRAPPER = '.ibexa-field-edit-preview__info';
    const SELECTOR_MEDIA_WRAPPER = '.ibexa-field-edit-preview__media-wrapper';
    const SELECTOR_INPUT_FILE = 'input[type="file"]';
    const CLASS_MEDIA_WRAPPER_LOADING = 'ibexa-field-edit-preview__media-wrapper--loading';
    const SELECTOR_FILESIZE_NOTICE = '.ibexa-data-source__message--filesize';

    class EzMediaValidator extends eZ.BaseFileFieldValidator {
        validateFileSize(event) {
            event.currentTarget.dispatchEvent(new CustomEvent('ibexa-invalid-file-size'));

            return {
                isError: false,
            };
        }

        /**
         * Validates the dimensions inputs
         *
         * @method validateDimensions
         * @param {Event} event
         * @returns {Object}
         * @memberof EzMediaValidator
         */
        validateDimensions(event) {
            const input = event.currentTarget;
            const isRequired = input.required;
            const value = parseInt(input.value, 10);
            const isEmpty = isNaN(value);
            const isInteger = Number.isInteger(value);
            const isError = (isEmpty && isRequired) || (!isEmpty && !isInteger);
            const label = input.closest(SELECTOR_INFO_WRAPPER).querySelector('.ibexa-field-edit-preview__label').innerHTML;
            const result = { isError };

            if (isEmpty) {
                result.errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);
            } else if (!isInteger) {
                result.errorMessage = eZ.errors.isNotInteger.replace('{fieldName}', label);
            }

            if (!input.closest('.ibexa-field-edit__preview').hasAttribute('hidden')) {
                return result;
            }

            return { isError: false };
        }
    }

    class EzMediaPreviewField extends eZ.BasePreviewField {
        /**
         * Loads dropped file preview
         *
         * @param {Event} event
         */
        loadDroppedFilePreview(event) {
            const preview = this.fieldContainer.querySelector('.ibexa-field-edit__preview');
            const nameContainer = preview.querySelector('.ibexa-field-edit-preview__file-name');
            const sizeContainer = preview.querySelector('.ibexa-field-edit-preview__file-size');
            const files = [].slice.call(event.target.files);
            const fileSize = this.formatFileSize(files[0].size);

            nameContainer.innerHTML = files[0].name;
            nameContainer.title = files[0].name;
            sizeContainer.innerHTML = fileSize;
            sizeContainer.title = fileSize;

            // it breaks the rendering flow and prevents from blocking
            // rendering a whole fieldtype container
            // while loading video source
            window.setTimeout(this.updateMediaSource.bind(this, files[0]), 100);
        }

        /**
         * Updates a value of media source (the video tag - src attribute)
         *
         * @method updateMediaSource
         * @param {File} file
         */
        updateMediaSource(file) {
            const preview = this.fieldContainer.querySelector(SELECTOR_PREVIEW);
            const videoUrl = URL.createObjectURL(file);

            preview.querySelector('.ibexa-field-edit-preview__action--preview').href = videoUrl;
            preview.querySelector(SELECTOR_MEDIA).setAttribute('src', videoUrl);
        }

        /**
         * Displays a media preview container
         *
         * @method showMediaPreview
         */
        showMediaPreview() {
            const mediaWrapper = this.fieldContainer.querySelector(SELECTOR_MEDIA_WRAPPER);

            mediaWrapper.classList.remove(CLASS_MEDIA_WRAPPER_LOADING);
        }

        /**
         * Displays a media loading container
         *
         * @method showMediaLoadingScreen
         */
        showMediaLoadingScreen() {
            const mediaWrapper = this.fieldContainer.querySelector(SELECTOR_MEDIA_WRAPPER);

            mediaWrapper.classList.add(CLASS_MEDIA_WRAPPER_LOADING);
        }

        /**
         * Handles file removing
         *
         * @method handleRemoveFile
         * @param {Event} event
         */
        handleRemoveFile(event) {
            super.handleRemoveFile(event);

            this.showMediaLoadingScreen();
        }

        /**
         * Initializes the preview
         *
         * @method init
         */
        init() {
            super.init();

            const preview = this.fieldContainer.querySelector(SELECTOR_PREVIEW);
            const video = preview.querySelector(SELECTOR_MEDIA);

            video.addEventListener('canplay', this.showMediaPreview.bind(this), false);
        }
    }

    doc.querySelectorAll(SELECTOR_FIELD).forEach((fieldContainer) => {
        const validator = new EzMediaValidator({
            classInvalid: 'is-invalid',
            fieldContainer,
            eventsMap: [
                {
                    isValueValidator: false,
                    selector: SELECTOR_INPUT_FILE,
                    eventName: 'ibexa-invalid-file-size',
                    callback: 'showFileSizeError',
                    errorNodeSelectors: [SELECTOR_FILESIZE_NOTICE],
                },
                {
                    selector: '.ibexa-field-edit-preview__dimensions .form-control',
                    eventName: 'blur',
                    callback: 'validateDimensions',
                    errorNodeSelectors: [`${SELECTOR_INFO_WRAPPER} .ibexa-form-error`],
                },
                {
                    selector: SELECTOR_INPUT_FILE,
                    eventName: 'change',
                    callback: 'validateInput',
                    errorNodeSelectors: ['.ibexa-field-edit--ezmedia .ibexa-form-error'],
                },
            ],
        });
        const previewField = new EzMediaPreviewField({
            validator,
            fieldContainer,
            fileTypeAccept: fieldContainer.querySelector(SELECTOR_INPUT_FILE).accept,
        });

        previewField.init();

        eZ.addConfig('fieldTypeValidators', [validator], true);
    });
})(window, window.document, window.eZ);
