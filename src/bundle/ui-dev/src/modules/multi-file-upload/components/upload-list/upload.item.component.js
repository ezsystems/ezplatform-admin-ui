import React, { Component } from 'react';
import PropTypes from 'prop-types';

import ProgressBarComponent from '../progress-bar/progress.bar.component';
import { fileSizeToString } from '../../helpers/text.helper';
import Icon from '../../../common/icon/icon';

export default class UploadItemComponent extends Component {
    constructor(props) {
        super(props);

        this.handleFileSizeNotAllowed = this.handleFileSizeNotAllowed.bind(this);
        this.handleFileTypeNotAllowed = this.handleFileTypeNotAllowed.bind(this);
        this.handleContentTypeNotAllowed = this.handleContentTypeNotAllowed.bind(this);
        this.handleEditBtnClick = this.handleEditBtnClick.bind(this);
        this.handleUploadAbort = this.handleUploadAbort.bind(this);
        this.handleUploadError = this.handleUploadError.bind(this);
        this.handleUploadLoad = this.handleUploadLoad.bind(this);
        this.handleUploadProgress = this.handleUploadProgress.bind(this);
        this.handleUploadEnd = this.handleUploadEnd.bind(this);
        this.handleLoadStart = this.handleLoadStart.bind(this);
        this.handleFileDeleted = this.handleFileDeleted.bind(this);
        this.abortUploading = this.abortUploading.bind(this);
        this.deleteFile = this.deleteFile.bind(this);
        this.contentInfoInput = null;
        this.contentVersionInfoInput = null;
        this.contentVersionNoInput = null;
        this.contentEditBtn = null;
        this.state = {
            uploading: false,
            uploaded: props.isUploaded,
            disallowed: false,
            disallowedType: false,
            disallowedSize: false,
            disallowedContentType: false,
            aborted: false,
            failed: false,
            deleted: false,
            progress: 0,
            xhr: null,
            struct: props.data.struct || null,
            totalSize: fileSizeToString(props.data.file.size),
            uploadedSize: '0',
        };
    }

    componentDidMount() {
        const {
            data,
            adminUiConfig,
            parentInfo,
            createFileStruct,
            isUploaded,
            checkCanUpload,
            contentCreatePermissionsConfig,
            currentLanguage,
        } = this.props;

        this.contentInfoInput = window.document.querySelector('#form_subitems_content_edit_content_info');
        this.contentVersionInfoInput = window.document.querySelector('#form_subitems_content_edit_version_info_content_info');
        this.contentVersionNoInput = window.document.querySelector('#form_subitems_content_edit_version_info_version_no');
        this.contentEditBtn = window.document.querySelector('#form_subitems_content_edit_create');

        if (isUploaded) {
            return;
        }

        const config = {
            ...adminUiConfig.multiFileUpload,
            contentCreatePermissionsConfig,
        };
        const callbacks = {
            fileTypeNotAllowedCallback: this.handleFileTypeNotAllowed,
            fileSizeNotAllowedCallback: this.handleFileSizeNotAllowed,
            contentTypeNotAllowedCallback: this.handleContentTypeNotAllowed,
        };

        if (!checkCanUpload(data.file, parentInfo, config, callbacks)) {
            this.setState(() => ({
                uploading: false,
                disallowed: true,
                uploaded: false,
                aborted: false,
                failed: true,
            }));

            return;
        }

        createFileStruct(data.file, {
            parentInfo,
            config: adminUiConfig,
            languageCode: currentLanguage,
        }).then(this.initPublishFile.bind(this, adminUiConfig));
    }

    /**
     * Initializes file-based content publishing
     *
     * @method initPublishFile
     * @param {Object} restInfo config object containing token and siteaccess properties
     * @param {Object} struct
     * @memberof UploadItemComponent
     */
    initPublishFile({ token, siteaccess }, struct) {
        this.props.publishFile(
            { struct, token, siteaccess },
            {
                upload: {
                    onabort: this.handleUploadAbort,
                    onerror: this.handleUploadError,
                    onload: this.handleUploadLoad,
                    onprogress: this.handleUploadProgress,
                },
                onloadstart: this.handleLoadStart,
                onerror: this.handleUploadError,
            },
            this.handleUploadEnd
        );
    }

    /**
     * Handles the case when a file cannot be upload because of file type
     *
     * @method handleFileTypeNotAllowed
     * @memberof UploadItemComponent
     */
    handleFileTypeNotAllowed() {
        this.setState(() => ({
            uploading: false,
            disallowed: true,
            disallowedType: true,
            disallowedSize: false,
            disallowedContentType: false,
            uploaded: false,
            aborted: false,
            failed: true,
        }));
    }

    /**
     * Handles the case when a file cannot be upload because of file size
     *
     * @method handleFileSizeNotAllowed
     * @memberof UploadItemComponent
     */
    handleFileSizeNotAllowed() {
        this.setState(() => ({
            uploading: false,
            disallowed: true,
            disallowedType: false,
            disallowedSize: true,
            disallowedContentType: false,
            uploaded: false,
            aborted: false,
            failed: true,
        }));
    }

    handleContentTypeNotAllowed() {
        this.setState(() => ({
            uploading: false,
            disallowed: true,
            disallowedType: false,
            disallowedSize: false,
            disallowedContentType: true,
            uploaded: false,
            aborted: false,
            failed: true,
        }));
    }

    /**
     * Handles the upload load start event
     *
     * @method handleLoadStart
     * @param {Event} event
     * @memberof UploadItemComponent
     */
    handleLoadStart(event) {
        this.setState(() => ({
            uploading: true,
            disallowed: false,
            disallowedType: false,
            disallowedSize: false,
            disallowedContentType: false,
            uploaded: false,
            aborted: false,
            failed: false,
            xhr: event.target,
        }));
    }

    /**
     * Handles the upload abort event
     *
     * @method handleUploadAbort
     * @memberof UploadItemComponent
     */
    handleUploadAbort() {
        this.setState(() => ({
            uploading: false,
            disallowed: false,
            disallowedType: false,
            disallowedSize: false,
            disallowedContentType: false,
            uploaded: false,
            aborted: true,
            failed: false,
        }));
    }

    /**
     * Handles the upload error event
     *
     * @method handleUploadError
     * @memberof UploadItemComponent
     */
    handleUploadError() {
        this.setState((state) => ({
            uploading: false,
            disallowed: state.disallowed,
            disallowedSize: state.disallowedSize,
            disallowedType: state.disallowedType,
            disallowedContentType: state.disallowedContentType,
            uploaded: false,
            aborted: state.aborted,
            failed: true,
        }));
    }

    /**
     * Handles the upload load event
     *
     * @method handleUploadLoad
     * @memberof UploadItemComponent
     */
    handleUploadLoad() {
        this.setState(() => ({
            uploading: false,
            disallowed: false,
            disallowedType: false,
            disallowedSize: false,
            disallowedContentType: false,
            uploaded: true,
            aborted: false,
            failed: false,
        }));
    }

    /**
     * Handles the upload progress event
     *
     * @method handleUploadProgress
     * @param {Event} event
     * @memberof UploadItemComponent
     */
    handleUploadProgress(event) {
        const fraction = event.loaded / event.total;
        const progress = parseInt(fraction * 100, 10);

        this.setState(() => ({
            uploadedSize: fileSizeToString(fraction * parseInt(this.props.data.file.size, 10)),
            uploading: true,
            disallowed: false,
            disallowedType: false,
            disallowedSize: false,
            disallowedContentType: false,
            uploaded: false,
            aborted: false,
            failed: false,
            progress,
        }));
    }

    /**
     * Handles the upload end event
     *
     * @method handleUploadEnd
     * @memberof UploadItemComponent
     */
    handleUploadEnd() {
        this.setState(
            (state) => {
                const struct = JSON.parse(state.xhr.response);

                return {
                    struct,
                    uploading: false,
                    disallowed: false,
                    disallowedType: false,
                    disallowedSize: false,
                    disallowedContentType: false,
                    uploaded: true,
                    aborted: false,
                    failed: false,
                };
            },
            () => {
                const data = this.props.data;

                this.props.onAfterUpload({ ...data, struct: this.state.struct });
            }
        );
    }

    /**
     * Aborts file upload
     *
     * @method abortUploading
     * @memberof UploadItemComponent
     */
    abortUploading() {
        this.state.xhr.abort();
        this.props.onAfterAbort(this.props.data);
    }

    /**
     * Deletes a file
     *
     * @method deleteFile
     * @memberof UploadItemComponent
     */
    deleteFile() {
        this.setState(
            () => ({ deleted: true }),
            () => this.props.deleteFile(this.props.adminUiConfig, this.state.struct, this.handleFileDeleted)
        );
    }

    /**
     * Handles the file deleted event
     *
     * @method handleFileDeleted
     * @memberof UploadItemComponent
     */
    handleFileDeleted() {
        this.props.onAfterDelete(this.props.data);
    }

    /**
     * Returns content type identifier
     * based on Content object returned from server after upload
     *
     * @method getContentTypeIdentifier
     * @memberof UploadItemComponent
     * @returns {String|null}
     */
    getContentTypeIdentifier() {
        const { contentTypesMap, data } = this.props;

        if (!data.struct || !data.struct.Content) {
            return null;
        }

        const contentTypeHref = data.struct.Content.ContentType._href;
        const contentType = contentTypesMap ? contentTypesMap[contentTypeHref] : null;
        const contentTypeIdentifier = contentType ? contentType.identifier : null;

        return contentTypeIdentifier;
    }

    /**
     * Renders an icon of a content type
     *
     * @method renderIcon
     * @returns {JSX.Element|null}
     */
    renderIcon() {
        const contentTypeIdentifier = this.getContentTypeIdentifier();

        if (!contentTypeIdentifier) {
            return null;
        }

        const contentTypeIconUrl = eZ.helpers.contentType.getContentTypeIconUrl(contentTypeIdentifier);

        return <Icon customPath={contentTypeIconUrl} extraClasses="ibexa-icon--small-medium ibexa-icon--base-dark" />;
    }

    /**
     * Renders a progress bar
     *
     * @method renderProgressBar
     * @memberof UploadItemComponent
     * @returns {null|Element}
     */
    renderProgressBar() {
        const { uploaded, aborted, progress, totalSize, uploadedSize, disallowed } = this.state;

        if (this.props.isUploaded || uploaded || aborted || disallowed) {
            return null;
        }

        return <ProgressBarComponent progress={progress} uploaded={uploadedSize} total={totalSize} />;
    }

    /**
     * Renders an error message
     *
     * @method renderErrorMessage
     * @memberof UploadItemComponent
     * @returns {null|Element}
     */
    renderErrorMessage() {
        const { uploaded, aborted, disallowedType, disallowedSize, failed, uploading, disallowedContentType } = this.state;
        const isError = !uploaded && !aborted && (disallowedSize || disallowedType || disallowedContentType) && failed && !uploading;
        const cannotUploadMessage = Translator.trans(/*@Desc("Cannot upload file")*/ 'cannot_upload.message', {}, 'multi_file_upload');
        const disallowedTypeMessage = Translator.trans(
            /*@Desc("File type is not allowed")*/ 'disallowed_type.message',
            {},
            'multi_file_upload'
        );
        const disallowedSizeMessage = Translator.trans(
            /*@Desc("File size is not allowed")*/ 'disallowed_size.message',
            {},
            'multi_file_upload'
        );
        const disallowedContentTypeMessage = Translator.trans(
            /*@Desc("You do not have permission to create this Content item")*/ 'disallowed_content_type.message',
            {},
            'multi_file_upload'
        );
        let msg = cannotUploadMessage;

        if (disallowedType) {
            msg = disallowedTypeMessage;
        }

        if (disallowedSize) {
            msg = disallowedSizeMessage;
        }

        if (disallowedContentType) {
            msg = disallowedContentTypeMessage;
        }

        return isError ? <div className="c-upload-list-item__message c-upload-list-item__message--error">{msg}</div> : null;
    }

    /**
     * Renders an error message
     *
     * @method renderErrorMessage
     * @memberof UploadItemComponent
     * @returns {null|Element}
     */
    renderSuccessMessage() {
        const { uploaded, aborted, disallowedSize, disallowedType, failed, uploading } = this.state;
        const isSuccess = uploaded && !aborted && !(disallowedSize || disallowedType) && !failed && !uploading;
        const message = Translator.trans(/*@Desc("Uploaded")*/ 'upload.success.message', {}, 'multi_file_upload');

        return isSuccess ? <div className="c-upload-list-item__message c-upload-list-item__message--success">{message}</div> : null;
    }

    /**
     * Renders an abort upload button
     *
     * @method renderAbortBtn
     * @memberof UploadItemComponent
     * @returns {null|Element}
     */
    renderAbortBtn() {
        const { uploaded, aborted, disallowedSize, disallowedType, failed, uploading } = this.state;
        const canAbort = !uploaded && !aborted && !disallowedSize && !disallowedType && !failed && uploading;

        if (!canAbort) {
            return null;
        }

        const label = Translator.trans(/*@Desc("Abort")*/ 'abort.label', {}, 'multi_file_upload');

        return (
            <div
                className="c-upload-list-item__action c-upload-list-item__action--abort"
                onClick={this.abortUploading}
                title={label}
                tabIndex="-1">
                <Icon name="circle-close" extraClasses="ibexa-icon--small-medium" />
            </div>
        );
    }

    /**
     * Handles the edit button click event. Fills in the hidden form to redirect a user to a correct content edit location.
     *
     * @method handleEditBtnClick
     * @memberof UploadItemComponent
     * @param {Event} event
     */
    handleEditBtnClick(event) {
        event.preventDefault();

        const { struct } = this.state;
        const content = struct.Content;
        const contentId = content._id;
        const languageCode = content.CurrentVersion.Version.VersionInfo.VersionTranslationInfo.Language['0'].languageCode;
        const versionNo = content.CurrentVersion.Version.VersionInfo.versionNo;

        this.contentInfoInput.value = contentId;
        this.contentVersionInfoInput.value = contentId;
        this.contentVersionNoInput.value = versionNo;
        window.document.querySelector(`#form_subitems_content_edit_language_${languageCode}`).checked = true;
        this.contentEditBtn.click();
    }

    /**
     * Renders an edit content button
     *
     * @method renderEditBtn
     * @memberof UploadItemComponent
     * @returns {null|Element}
     */
    renderEditBtn() {
        const { uploaded, aborted, disallowedSize, disallowedType, failed, uploading } = this.state;
        const canEdit = this.props.isUploaded || (uploaded && !aborted && !(disallowedSize || disallowedType) && !failed && !uploading);

        if (!canEdit) {
            return null;
        }

        const label = Translator.trans(/*@Desc("Edit")*/ 'edit.label', {}, 'multi_file_upload');

        return (
            <div
                className="c-upload-list-item__action c-upload-list-item__action--edit"
                title={label}
                onClick={this.handleEditBtnClick}
                tabIndex="-1">
                <Icon name="edit" extraClasses="ibexa-icon--small-medium" />
            </div>
        );
    }

    /**
     * Renders an delete content button
     *
     * @method renderDeleteBtn
     * @memberof UploadItemComponent
     * @returns {null|Element}
     */
    renderDeleteBtn() {
        const { uploaded, aborted, disallowedSize, disallowedType, failed, uploading } = this.state;
        const canDelete = this.props.isUploaded || (uploaded && !aborted && !(disallowedSize || disallowedType) && !failed && !uploading);

        if (!canDelete) {
            return null;
        }

        const label = Translator.trans(/*@Desc("Delete")*/ 'delete.label', {}, 'multi_file_upload');

        return (
            <div
                className="c-upload-list-item__action c-upload-list-item__action--delete"
                onClick={this.deleteFile}
                title={label}
                tabIndex="-1">
                <Icon name="trash" extraClasses="ibexa-icon--small-medium" />
            </div>
        );
    }

    render() {
        if (this.state.deleted) {
            return null;
        }

        return (
            <div className="c-upload-list-item">
                <div className="c-upload-list-item__icon-wrapper">{this.renderIcon()}</div>
                <div className="c-upload-list-item__meta">
                    <div className="c-upload-list-item__name">{this.props.data.file.name}</div>
                    <div className="c-upload-list-item__size">{this.state.uploaded ? this.state.totalSize : ''}</div>
                </div>
                <div className="c-upload-list-item__info">
                    {this.renderErrorMessage()}
                    {this.renderSuccessMessage()}
                    {this.renderProgressBar()}
                </div>
                <div className="c-upload-list-item__actions">
                    {this.renderAbortBtn()}
                    {this.renderEditBtn()}
                    {this.renderDeleteBtn()}
                </div>
            </div>
        );
    }
}

UploadItemComponent.propTypes = {
    data: PropTypes.object.isRequired,
    onAfterUpload: PropTypes.func.isRequired,
    onAfterAbort: PropTypes.func.isRequired,
    onAfterDelete: PropTypes.func.isRequired,
    isUploaded: PropTypes.bool.isRequired,
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
    contentCreatePermissionsConfig: PropTypes.object,
    contentTypesMap: PropTypes.object.isRequired,
    currentLanguage: PropTypes.string,
};

UploadItemComponent.defaultProps = {
    isUploaded: false,
    currentLanguage: '',
};
