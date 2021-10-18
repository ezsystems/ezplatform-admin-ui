(function(global, doc, eZ) {
    const SELECTOR_DATA = '.ibexa-field-edit__data';
    const SELECTOR_PREVIEW = '.ibexa-field-edit__preview';
    const SELECTOR_BTN_REMOVE = '.ibexa-field-edit-preview__action--remove';

    class BasePreviewField {
        constructor({ fieldContainer, allowedFileTypes, fileTypeAccept, validator }) {
            this.fieldContainer = fieldContainer || null;
            this.allowedFileTypes = allowedFileTypes || [];
            this.fileTypeAccept = fileTypeAccept || '';
            this.validator = validator;
            this.inputField = this.findInputField();
            this.clonedInputField = this.inputField.cloneNode(true);
            this.openFileSelector = this.openFileSelector.bind(this);
            this.showPreview = this.showPreview.bind(this);
            this.handleRemoveFile = this.handleRemoveFile.bind(this);
            this.handleDropFile = this.handleDropFile.bind(this);
            this.handleInputChange = this.handleInputChange.bind(this);

            const dataMaxSize = +this.inputField.dataset.maxFileSize;

            this.maxFileSize = parseInt(dataMaxSize, 10);
            this.showPreviewEventName = 'ibexa-base:show-preview';
        }

        /**
         * Formats a file size information
         *
         * @method formatFileSize
         * @param {Number} bytes file size in bytes
         * @return {String} formatted file size information
         */
        formatFileSize(bytes) {
            const units = ['B', 'KB', 'MB', 'GB'];
            const kilobyte = 1024;
            let size = parseInt(bytes, 10) || 0;
            let unitIndex = 0;
            let decimalUnits;

            while (size >= kilobyte) {
                size = size / kilobyte;
                unitIndex++;
            }

            decimalUnits = unitIndex < 1 ? 0 : 1;

            const sizeFixed = size.toFixed(size >= 10 || decimalUnits);
            const unit = units[unitIndex];

            return `${sizeFixed} ${unit}`;
        }

        /**
         * Finds an input element in a field container
         *
         * @method findInputField
         */
        findInputField() {
            return this.fieldContainer.querySelector('input[type="file"]');
        }

        /**
         * Opens a native file selector
         *
         * @method openFileSelector
         * @param {Event} event
         */
        openFileSelector(event) {
            event.preventDefault();

            this.inputField.click();
        }

        /**
         * Handles dropping files
         *
         * @method handleDropFiles
         * @param {Event} event
         */
        handleDropFile(event) {
            const file = event.dataTransfer.files[0];

            if (!this.checkCanDrop(file)) {
                return;
            }

            if (this.maxFileSize > 0 && file.size > this.maxFileSize) {
                return this.showFileSizeError();
            }

            const changeEvent = new Event('change');

            this.inputField.files = event.dataTransfer.files;
            this.inputField.dispatchEvent(changeEvent);
        }

        /**
         * Displays a file size error
         *
         * @method showFileSizeError
         */
        showFileSizeError() {
            this.inputField.dispatchEvent(new CustomEvent('ibexa-invalid-file-size'));
        }

        /**
         * Checks whether a given file can be dropped onto a field
         *
         * @method checkCanDrop
         * @param {File} file
         * @returns {Boolean}
         */
        checkCanDrop(file) {
            const accept = this.fileTypeAccept;

            if (!this.allowedFileTypes.length && !accept.length) {
                return true;
            }

            if (accept.length && accept.includes('/*')) {
                const pattern = accept.substr(0, accept.indexOf('*'));

                return file.type.includes(pattern);
            }

            return this.allowedFileTypes.includes(file.type);
        }

        /**
         * Checks if file size is an allowed limit
         *
         * @method handleInputChange
         * @param {Event} event
         */
        handleInputChange(event) {
            if (this.maxFileSize > 0 && event.currentTarget.files[0].size > this.maxFileSize) {
                return this.resetInputField();
            }

            this.fieldContainer.querySelector('.ibexa-field-edit__option--remove-media').checked = false;

            this.showPreview(event);
        }

        /**
         * Displays a file preview
         *
         * @method showPreview
         * @param {Event} [event]
         */
        showPreview(event) {
            const btnRemove = this.fieldContainer.querySelector(SELECTOR_BTN_REMOVE);
            const dropZone = this.fieldContainer.querySelector(SELECTOR_DATA);

            if (event) {
                this.loadDroppedFilePreview(event);
            }

            this.fieldContainer.querySelector(SELECTOR_PREVIEW).removeAttribute('hidden');
            dropZone.setAttribute('hidden', true);

            btnRemove.addEventListener('click', this.handleRemoveFile, false);
            dropZone.removeEventListener('drop', this.handleDropFile);
        }

        /**
         * Loads dropped file preview.
         * It should redefined in each class that extends this one.
         *
         * @method loadDroppedFilePreview
         * @param {Event} event
         */
        loadDroppedFilePreview(event) {
            console.log('CUSTOMIZE RENDERING DROPPED FILE PREVIEW', event);
        }

        /**
         * Hides a file preview
         *
         * @method hidePreview
         */
        hidePreview() {
            const btnRemove = this.fieldContainer.querySelector(SELECTOR_BTN_REMOVE);

            this.fieldContainer.querySelector(SELECTOR_DATA).removeAttribute('hidden');
            this.fieldContainer.querySelector(SELECTOR_PREVIEW).setAttribute('hidden', true);
            this.fieldContainer.classList.remove('is-invalid');
            this.fieldContainer.querySelectorAll('.ibexa-field-edit__error').forEach((element) => element.remove());

            btnRemove.removeEventListener('click', this.handleRemoveFile);

            this.initializeDropZone();
        }

        /**
         * Resets input field state
         *
         * @method resetInputField
         */
        resetInputField() {
            const clonedInput = this.clonedInputField.cloneNode(true);

            // required to reset properly the input of file type properly
            this.inputField.parentNode.replaceChild(clonedInput, this.inputField);
            this.inputField = clonedInput;
            this.inputField.addEventListener('change', this.handleInputChange, false);
            this.fieldContainer.querySelector('.ibexa-field-edit__option--remove-media').checked = true;

            this.validator.reinit();
        }

        /**
         * Removes a file from input and hides a preview afterwards
         *
         * @method handleRemoveFile
         */
        handleRemoveFile(event) {
            event.preventDefault();

            this.resetInputField();
            this.hidePreview();
        }

        /**
         * Prevents from executing default actions
         *
         * @method preventDefaultAction
         * @param {Object} event
         */
        preventDefaultAction(event) {
            event.preventDefault();
            event.stopPropagation();
        }

        /**
         * Initializes drop zone event handlers
         *
         * @method initializeDropZone
         */
        initializeDropZone() {
            const dropZone = this.fieldContainer.querySelector('.ibexa-field-edit__preview[hidden] + .ibexa-field-edit__data');

            if (dropZone) {
                dropZone.addEventListener('drop', this.handleDropFile, false);
            }
        }

        /**
         * Initializes the preview
         *
         * @method initializePreview
         */
        initializePreview() {
            const preview = this.fieldContainer.querySelector('.ibexa-field-edit__preview');

            if (!preview.hasAttribute('hidden')) {
                this.showPreview();
            }
        }

        /**
         * Initializes the preview
         *
         * @method init
         */
        init() {
            this.btnAdd = this.fieldContainer.querySelector('.ibexa-data-source__btn-add');

            this.btnAdd.addEventListener('click', this.openFileSelector, false);
            this.inputField.addEventListener('change', this.handleInputChange, false);
            window.addEventListener('drop', this.preventDefaultAction, false);
            window.addEventListener('dragover', this.preventDefaultAction, false);

            this.fieldContainer.addEventListener(this.showPreviewEventName, () => this.showPreview());

            this.initializeDropZone();
            this.initializePreview();

            this.validator.init();
        }
    }

    eZ.addConfig('BasePreviewField', BasePreviewField);
})(window, window.document, window.eZ);
