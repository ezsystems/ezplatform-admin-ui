import React, { Component } from 'react';
import PropTypes from 'prop-types';

import UploadPopupComponent from './components/upload-popup/upload.popup.component';
import { createFileStruct, publishFile, deleteFile, checkCanUpload } from './services/multi.file.upload.service';
import Icon from '../common/icon/icon';

export default class MultiFileUploadModule extends Component {
    constructor(props) {
        super(props);

        let popupVisible = true;

        this._itemsUploaded = [];

        if (!props.itemsToUpload || !props.itemsToUpload.length) {
            popupVisible = false;
        }

        this.handleDropOnWindow = this.handleDropOnWindow.bind(this);
        this.handleAfterUpload = this.handleAfterUpload.bind(this);
        this.showUploadPopup = this.showUploadPopup.bind(this);
        this.hidePopup = this.hidePopup.bind(this);
        this.processUploadedFiles = this.processUploadedFiles.bind(this);
        this.setUdwStateOpened = this.setUdwStateOpened.bind(this);
        this.setUdwStateClosed = this.setUdwStateClosed.bind(this);

        this.state = {
            udwOpened: false,
            popupVisible,
            itemsToUpload: props.itemsToUpload,
            allowDropOnWindow: true,
            uploadDisabled: Object.values(props.contentCreatePermissionsConfig).every((isEnabled) => !isEnabled),
        };
    }

    componentDidMount() {
        this.manageDropEvent();

        window.document.body.addEventListener('ez-udw-opened', this.setUdwStateOpened, false);
        window.document.body.addEventListener('ez-udw-closed', this.setUdwStateClosed, false);
    }

    componentDidUpdate() {
        this.manageDropEvent();
    }

    componentWillUnmount() {
        window.document.body.removeEventListener('ez-udw-opened', this.setUdwStateOpened, false);
        window.document.body.removeEventListener('ez-udw-closed', this.setUdwStateClosed, false);
    }

    /**
     * Set udw state as open
     *
     * @method setUdwStateOpened
     * @memberof MultiFileUploadModule
     */
    setUdwStateOpened() {
        this.setState({ udwOpened: true });
    }

    /**
     * Set udw state as closed
     *
     * @method setUdwStateClosed
     * @memberof MultiFileUploadModule
     */
    setUdwStateClosed() {
        this.setState({ udwOpened: false });
    }

    /**
     * Attaches `drop` and `dragover` events handlers on window
     *
     * @method manageDropEvent
     * @memberof MultiFileUploadModule
     */
    manageDropEvent() {
        const { uploadDisabled, popupVisible, itemsToUpload } = this.state;

        if (!uploadDisabled && !popupVisible && !itemsToUpload.length) {
            window.addEventListener('drop', this.handleDropOnWindow, false);
            window.addEventListener('dragover', this.preventDefaultAction, false);
        }
    }

    /**
     * Hides multi file upload popup
     *
     * @method hidePopup
     * @memberof MultiFileUploadModule
     */
    hidePopup() {
        this.setState((state) => Object.assign({}, state, { popupVisible: false }));

        this.props.onPopupClose(this._itemsUploaded);
    }

    /**
     * Displays multi file upload popup
     *
     * @method showUploadPopup
     * @memberof MultiFileUploadModule
     */
    showUploadPopup() {
        this.setState((state) =>
            Object.assign({}, state, {
                popupVisible: true,
                itemsToUpload: [],
            })
        );
    }

    /**
     * Keeps information about uploaded files.
     * We want to avoid component rerendering so it's stored in an object instance property.
     *
     * @method handleAfterUpload
     * @param {Array} itemsUploaded
     * @memberof MultiFileUploadModule
     */
    handleAfterUpload(itemsUploaded) {
        this._itemsUploaded = itemsUploaded;
    }

    /**
     * Handles dropping on window.
     * When file/files are dropped onto window the `drop` and `dragover` event handlers are removed.
     *
     * @method handleDropOnWindow
     * @param {Event} event
     * @memberof MultiFileUploadModule
     */
    handleDropOnWindow(event) {
        this.preventDefaultAction(event);

        const itemsToUpload = this.processUploadedFiles(event);

        // Covers the case when dragging and dropping page elements inside the browser,
        // like links, images, etc.
        if (!this.state.allowDropOnWindow || !itemsToUpload.length || this.state.udwOpened) {
            return;
        }

        window.removeEventListener('drop', this.handleDropOnWindow, false);
        window.removeEventListener('dragover', this.preventDefaultAction, false);

        this.setState((state) =>
            Object.assign({}, state, {
                itemsToUpload,
                popupVisible: true,
                allowDropOnWindow: false,
            })
        );
    }

    /**
     * Extracts information about dropped files
     *
     * @method extractDroppedFilesList
     * @param {Event} event
     * @returns {undefined|Array}
     * @memberof MultiFileUploadModule
     */
    extractDroppedFilesList(event) {
        let list;

        if (event.nativeEvent) {
            list = event.nativeEvent.dataTransfer || event.nativeEvent.target;
        } else {
            list = event.dataTransfer;
        }

        return list;
    }

    /**
     * Processes uploaded files and generates an unique file id
     *
     * @method processUploadedFiles
     * @param {Event} event
     * @returns {Array}
     * @memberof MultiFileUploadModule
     */
    processUploadedFiles(event) {
        const list = this.extractDroppedFilesList(event);

        return Array.from(list.files).map((file) => ({
            id: Math.floor(Math.random() * Date.now()),
            file,
        }));
    }

    /**
     * Prevents default event actions
     *
     * @method preventDefaultAction
     * @param {Event} event
     * @memberof MultiFileUploadModule
     */
    preventDefaultAction(event) {
        event.preventDefault();
        event.stopPropagation();
    }

    /**
     * Renders multi file upload button,
     * that allows to open multi file upload popup.
     *
     * @method renderBtn
     * @returns {null|Element}
     * @memberof MultiFileUploadModule
     */
    renderBtn() {
        if (!this.props.withUploadButton) {
            return null;
        }

        const uploadDisabled = this.state.uploadDisabled;
        const label = Translator.trans(/*@Desc("Upload")*/ 'multi_file_upload_open_btn.label', {}, 'multi_file_upload');

        return (
            <button
                type="button"
                className="btn ibexa-btn ibexa-btn--ghost"
                onClick={this.showUploadPopup}
                disabled={uploadDisabled}>
                <Icon name="upload" extraClasses="ibexa-icon--small" /> {label}
            </button>
        );
    }

    /**
     * Renders a popup
     *
     * @method renderPopup
     * @returns {null|Element}
     * @memberof MultiFileUploadModule
     */
    renderPopup() {
        if (!this.state.popupVisible) {
            return null;
        }

        const attrs = {
            ...this.props,
            visible: true,
            onClose: this.hidePopup,
            itemsToUpload: this.state.itemsToUpload,
            onAfterUpload: this.handleAfterUpload,
            preventDefaultAction: this.preventDefaultAction,
            processUploadedFiles: this.processUploadedFiles,
        };

        return <UploadPopupComponent {...attrs} />;
    }

    render() {
        return (
            <div className="m-mfu">
                {this.renderBtn()}
                {this.renderPopup()}
            </div>
        );
    }
}

eZ.addConfig('modules.MultiFileUpload', MultiFileUploadModule);

MultiFileUploadModule.propTypes = {
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
    checkCanUpload: PropTypes.func,
    createFileStruct: PropTypes.func,
    deleteFile: PropTypes.func,
    onPopupClose: PropTypes.func,
    publishFile: PropTypes.func,
    itemsToUpload: PropTypes.array,
    withUploadButton: PropTypes.bool,
    contentCreatePermissionsConfig: PropTypes.object,
    contentTypesMap: PropTypes.object.isRequired,
    currentLanguage: PropTypes.string,
};

MultiFileUploadModule.defaultProps = {
    checkCanUpload,
    createFileStruct,
    deleteFile,
    onPopupClose: () => {},
    publishFile,
    itemsToUpload: [],
    withUploadButton: true,
    currentLanguage: '',
};
