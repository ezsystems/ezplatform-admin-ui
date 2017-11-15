(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit--ezmedia';
    const SELECTOR_PREVIEW = '.ez-field-edit__preview';
    const SELECTOR_MEDIA = '.ez-field-edit-preview__media';
    const SELECTOR_MEDIA_WRAPPER = '.ez-field-edit-preview__media-wrapper';
    const CLASS_MEDIA_WRAPPER_LOADING = 'ez-field-edit-preview__media-wrapper--loading';

    class EzMediaValidator extends global.eZ.BaseFileFieldValidator {
        /**
         * Updates the state of checkbox indicator.
         *
         * @method updateState
         * @param {Event} event
         * @memberof EzBooleanValidator
         */
        updateState(event) {
            const checkbox = event.currentTarget.querySelector('input[type="checkbox"]');

            if (!checkbox) {
                return;
            }

            const label = event.currentTarget.querySelector('.ez-data-source__label');
            const methodName = checkbox.checked ? 'remove' : 'add';

            label.classList[methodName]('is-checked');
            checkbox.checked = !checkbox.checked;
        }
    }

    class EzMediaPreviewField extends global.eZ.BasePreviewField {
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

            preview.querySelector('.ez-field-edit-preview__action--preview').href = videoUrl;
            preview.querySelector(SELECTOR_MEDIA).setAttribute('src', videoUrl)
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

    [...document.querySelectorAll(SELECTOR_FIELD)].forEach(fieldContainer => {
        const validator = new EzMediaValidator({
            classInvalid: 'is-invalid',
            fieldContainer,
            eventsMap: [
                {
                    selector: '[type="file"]',
                    eventName: 'change',
                    callback: 'validateInput',
                    invalidStateSelectors: [SELECTOR_FIELD],
                    errorNodeSelectors: ['.ez-field-edit__label-wrapper'],
                }, {
                    selector: '.ez-field-edit-preview__info',
                    eventName: 'click',
                    callback: 'updateState',
                }, {
                    selector: '[type="file"]',
                    eventName: 'invalidFileSize',
                    callback: 'showSizeError',
                    invalidStateSelectors: [SELECTOR_FIELD],
                    errorNodeSelectors: ['.ez-field-edit__label-wrapper'],
                },
            ],
        });
        const previewField = new EzMediaPreviewField({
            validator,
            fieldContainer,
            fileTypeAccept: fieldContainer.querySelector('input[type="file"]').accept
        });

        previewField.init();
    })
})(window);
