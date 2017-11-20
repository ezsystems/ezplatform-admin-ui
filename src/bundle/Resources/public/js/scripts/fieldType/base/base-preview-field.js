(function (global) {
    const eZ = global.eZ = global.eZ || {};
    const SELECTOR_DATA = '.ez-field-edit__data';
    const SELECTOR_PREVIEW = '.ez-field-edit__preview';
    const SELECTOR_BTN_REMOVE = '.ez-field-edit-preview__action--remove';

    eZ.BasePreviewField = class BasePreviewField {
        constructor({fieldContainer, allowedFileTypes, fileTypeAccept, validator}) {
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
            this.checkFileSize = this.checkFileSize.bind(this);
            this.maxFileSize = parseInt(this.inputField.dataset.maxFileSize, 10);
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

            while(size >= kilobyte) {
                size = size / kilobyte;
                unitIndex++;
            }

            decimalUnits = unitIndex < 1 ? 0 : 1;

            return (size.toFixed(size >= 10 || decimalUnits) + ' ' + units[unitIndex]);
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
            if (!this.checkCanDrop(event.dataTransfer.files[0])) {
                return;
            }

            this.inputField.files = event.dataTransfer.files;
        }

        /**
         * Displays a file size error
         *
         * @method showFileSizeError
         */
        showFileSizeError() {
            this.inputField.dispatchEvent(new CustomEvent('invalidFileSize'));
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
         * @method checkFileSize
         * @param {Event} event
         */
        checkFileSize(event) {
            const file = [...event.currentTarget.files][0];

            if (this.maxFileSize !== 0 && file.size > this.maxFileSize) {
                return this.showFileSizeError();
            }

            this.showPreview(event);
        }

        /**
         * Displays a file preview
         *
         * @method showPreview
         * @param {Event} event
         */
        showPreview(event) {
            const btnRemove = this.fieldContainer.querySelector(SELECTOR_BTN_REMOVE);
            const dropZone = this.fieldContainer.querySelector(SELECTOR_DATA);

            if (event) {
                this.loadDroppedFilePreview(event);
            }

            this.fieldContainer.querySelector(SELECTOR_PREVIEW).classList.remove('ez-visually-hidden');
            dropZone.classList.add('ez-visually-hidden', true);

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

            this.fieldContainer.querySelector(SELECTOR_DATA).classList.remove('ez-visually-hidden');
            this.fieldContainer.querySelector(SELECTOR_PREVIEW).classList.add('ez-visually-hidden', true);

            btnRemove.removeEventListener('click', this.handleRemoveFile);

            this.initializeDropZone();
        }

        /**
         * Removes a file from input and hides a preview afterwards
         *
         * @method handleRemoveFile
         */
        handleRemoveFile(event) {
            event.preventDefault();

            const clonedInput = this.clonedInputField.cloneNode(true);

            // required to reset properly the input of file type properly
            this.inputField.parentNode.replaceChild(clonedInput, this.inputField);
            this.inputField = clonedInput;
            this.inputField.addEventListener('change', this.showPreview, false);

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
            const dropZone = this.fieldContainer.querySelector('.ez-field-edit__preview.ez-visually-hidden + .ez-field-edit__data');

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
            const preview = this.fieldContainer.querySelector('.ez-field-edit__preview');

            if (!preview.classList.contains('ez-visually-hidden')) {
                this.showPreview();
            }
        }

        /**
         * Initializes the preview
         *
         * @method init
         */
        init() {
            this.btnAdd = this.fieldContainer.querySelector('.ez-data-source__btn-add');

            this.btnAdd.addEventListener('click', this.openFileSelector, false);
            this.inputField.addEventListener('change', this.checkFileSize, false);
            window.addEventListener('drop', this.preventDefaultAction, false);
            window.addEventListener('dragover', this.preventDefaultAction, false);

            this.initializeDropZone();
            this.initializePreview();

            this.validator.init();
        }
    };
})(window, document);
