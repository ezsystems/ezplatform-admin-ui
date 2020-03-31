import React, { Component } from 'react';
import PropTypes from 'prop-types';

import TooltipPopup from '../../../common/tooltip-popup/tooltip.popup.component';
import DropAreaComponent from '../drop-area/drop.area.component';
import UploadListComponent from '../upload-list/upload.list.component';

const CLASS_SCROLL_DISABLED = 'ez-scroll-disabled';

export default class UploadPopupModule extends Component {
    constructor(props) {
        super(props);

        this.uploadFiles = this.uploadFiles.bind(this);
        this.refTooltip = React.createRef();
        this.state = { itemsToUpload: props.itemsToUpload };
    }

    componentDidMount() {
        window.document.body.classList.add(CLASS_SCROLL_DISABLED);
        window.eZ.helpers.tooltips.parse(this.refTooltip.current);
    }

    componentWillUnmount() {
        window.document.body.classList.remove(CLASS_SCROLL_DISABLED);
    }

    UNSAFE_componentWillReceiveProps(props) {
        this.setState((state) => {
            const stateItems = state.itemsToUpload.filter(
                (stateItem) => !props.itemsToUpload.find((propItem) => propItem.id === stateItem.id)
            );

            return { itemsToUpload: [...stateItems, ...props.itemsToUpload] };
        });
    }

    /**
     * Uploads files
     *
     * @method uploadFiles
     * @param {Array} itemsToUpload
     * @memberof UploadPopupModule
     */
    uploadFiles(itemsToUpload) {
        this.setState(() => ({ itemsToUpload }));
    }

    render() {
        const tooltipAttrs = this.props;
        const listAttrs = {
            ...tooltipAttrs,
            itemsToUpload: this.state.itemsToUpload,
        };
        const title = Translator.trans(/*@Desc("Multi-file upload")*/ 'upload_popup.close', {}, 'multi_file_upload');

        return (
            <div className="c-upload-popup" ref={this.refTooltip}>
                <TooltipPopup title={title} {...tooltipAttrs}>
                    <DropAreaComponent
                        onDrop={this.uploadFiles}
                        maxFileSize={this.props.adminUiConfig.multiFileUpload.maxFileSize}
                        preventDefaultAction={this.props.preventDefaultAction}
                        processUploadedFiles={this.props.processUploadedFiles}
                    />
                    <UploadListComponent {...listAttrs} />
                </TooltipPopup>
            </div>
        );
    }
}

UploadPopupModule.propTypes = {
    popupTitle: PropTypes.string.isRequired,
    visible: PropTypes.bool,
    onUpload: PropTypes.func,
    onUploadEnd: PropTypes.func,
    onUploadFail: PropTypes.func,
    onItemEdit: PropTypes.func,
    onItemRemove: PropTypes.func,
    onClose: PropTypes.func,
    itemsToUpload: PropTypes.array,
    onAfterUpload: PropTypes.func.isRequired,
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
    preventDefaultAction: PropTypes.func.isRequired,
    processUploadedFiles: PropTypes.func.isRequired,
    contentTypesMap: PropTypes.object.isRequired,
    currentLanguage: PropTypes.string,
};

UploadPopupModule.defaultProps = {
    visible: true,
    itemsToUpload: [],
    currentLanguage: '',
};
