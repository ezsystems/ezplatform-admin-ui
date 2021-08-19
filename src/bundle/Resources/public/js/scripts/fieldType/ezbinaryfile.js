(function(global, doc, eZ) {
    const SELECTOR_FIELD = '.ibexa-field-edit--ezbinaryfile';
    const SELECTOR_FILESIZE_NOTICE = '.ibexa-data-source__message--filesize';

    class EzBinaryFilePreviewField extends eZ.BasePreviewField {
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

            preview.querySelector('.ibexa-field-edit-preview__action--preview').href = URL.createObjectURL(files[0]);
        }
    }

    class EzBinaryFileFieldValidator extends eZ.BaseFileFieldValidator {
        validateFileSize(event) {
            event.currentTarget.dispatchEvent(new CustomEvent('ibexa-invalid-file-size'));

            return {
                isError: false,
            };
        }
    }

    doc.querySelectorAll(SELECTOR_FIELD).forEach((fieldContainer) => {
        const validator = new EzBinaryFileFieldValidator({
            classInvalid: 'is-invalid',
            fieldContainer,
            eventsMap: [
                {
                    selector: `input[type="file"]`,
                    eventName: 'change',
                    callback: 'validateInput',
                    errorNodeSelectors: ['.ibexa-form-error'],
                },
                {
                    isValueValidator: false,
                    selector: `input[type="file"]`,
                    eventName: 'ibexa-invalid-file-size',
                    callback: 'showFileSizeError',
                    errorNodeSelectors: [SELECTOR_FILESIZE_NOTICE],
                },
            ],
        });
        const previewField = new EzBinaryFilePreviewField({
            validator,
            fieldContainer,
        });

        previewField.init();

        eZ.addConfig('fieldTypeValidators', [validator], true);
    });
})(window, window.document, window.eZ);
