(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit--ezbinaryfile';
    const SELECTOR_LABEL_WRAPPER = '.ez-field-edit__label-wrapper';

    class EzBinaryFilePreviewField extends global.eZ.BasePreviewField {
        /**
         * Gets an icon according to a file type
         *
         * @method getIcon
         * @param {String} type
         * @returns {String} SVG icon markup
         */
        getIcon(type) {
            let name = 'file';

            if (type === 'application/pdf') {
                name = 'pdf-file';
            } else if (type.includes('image/')) {
                name = 'image';
            } else if (type.includes('video/')) {
                name = 'file-video';
            }

            return `
                <svg class="ez-icon">
                    <use xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#${name}"></use>
                </svg>
            `;
        }

        /**
         * Loads dropped file preview
         *
         * @param {Event} event
         */
        loadDroppedFilePreview(event) {
            const preview = this.fieldContainer.querySelector('.ez-field-edit__preview');
            const nameContainer = preview.querySelector('.ez-field-edit-preview__file-name');
            const sizeContainer = preview.querySelector('.ez-field-edit-preview__file-size');
            const files = [].slice.call(event.target.files);
            const fileSize = this.formatFileSize(files[0].size);

            nameContainer.innerHTML = files[0].name;
            nameContainer.title = files[0].name;
            sizeContainer.innerHTML = fileSize;
            sizeContainer.title = fileSize;

            preview.querySelector('.ez-field-edit-preview__file-icon').innerHTML = this.getIcon(files[0].type);
            preview.querySelector('.ez-field-edit-preview__action--preview').href = URL.createObjectURL(files[0]);
        }
    }

    [...document.querySelectorAll(SELECTOR_FIELD)].forEach(fieldContainer => {
        const validator = new global.eZ.BaseFileFieldValidator({
            classInvalid: 'is-invalid',
            fieldContainer,
            eventsMap: [
                {
                    selector: `input[type="file"]`,
                    eventName: 'change',
                    callback: 'validateInput',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                },
                {
                    isValueValidator: false,
                    selector: `input[type="file"]`,
                    eventName: 'invalidFileSize',
                    callback: 'showFileSizeError',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                },
            ],
        });
        const previewField = new EzBinaryFilePreviewField({
            validator,
            fieldContainer
        });

        previewField.init();

        global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
            [...global.eZ.fieldTypeValidators, validator] :
            [validator];
    })
})(window);
