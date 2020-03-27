import React, { Component } from 'react';
import PropTypes from 'prop-types';
import List from '../list/list.component';
import Icon from '../../../common/icon/icon';

const CLASS_IS_TREE_RESIZING = 'ez-is-tree-resizing';

export default class ContentTree extends Component {
    constructor(props) {
        super(props);

        this.changeContainerWidth = this.changeContainerWidth.bind(this);
        this.addWidthChangeListener = this.addWidthChangeListener.bind(this);
        this.handleResizeEnd = this.handleResizeEnd.bind(this);
        this._refTreeContainer = React.createRef();

        this.state = {
            resizeStartPositionX: 0,
            containerWidth: 0,
            resizedContainerWidth: 0,
            isResizing: false,
        };
    }

    componentWillUnmount() {
        this.clearDocumentResizingListeners();
    }

    componentDidUpdate(prevState) {
        if (this.state.containerWidth !== prevState.containerWidth) {
            document.body.dispatchEvent(new CustomEvent('ez-content-tree-resized'));
        }
    }

    changeContainerWidth({ clientX }) {
        const currentPositionX = clientX;

        this.setState((state) => ({
            resizedContainerWidth: state.containerWidth + (currentPositionX - state.resizeStartPositionX),
        }));
    }

    addWidthChangeListener({ nativeEvent }) {
        const resizeStartPositionX = nativeEvent.clientX;
        const containerWidth = this._refTreeContainer.current.getBoundingClientRect().width;

        window.document.addEventListener('mousemove', this.changeContainerWidth, false);
        window.document.addEventListener('mouseup', this.handleResizeEnd, false);
        window.document.body.classList.add(CLASS_IS_TREE_RESIZING);

        this.setState(() => ({ resizeStartPositionX, containerWidth, isResizing: true }));
    }

    handleResizeEnd() {
        this.clearDocumentResizingListeners();

        this.setState((state) => ({
            resizeStartPositionX: 0,
            containerWidth: state.resizedContainerWidth,
            isResizing: false,
        }));
    }

    clearDocumentResizingListeners() {
        window.document.removeEventListener('mousemove', this.changeContainerWidth);
        window.document.removeEventListener('mouseup', this.handleResizeEnd);
        window.document.body.classList.remove(CLASS_IS_TREE_RESIZING);
    }

    renderNoSubitemMessage() {
        const { items } = this.props;
        const rootLocation = items[0];
        const isRootLoaded = rootLocation;
        const noSubitemsMessage = Translator.trans(/*@Desc("This Location has no sub-items")*/ 'no_subitems', {}, 'content_tree');

        if (!isRootLoaded || (rootLocation.subitems && rootLocation.subitems.length)) {
            return;
        }

        return <div className="m-tree__no-items-message">{noSubitemsMessage}</div>;
    }

    renderCollapseAllBtn() {
        const collapseAllLabel = Translator.trans(/*@Desc("Collapse all")*/ 'collapse_all', {}, 'content_tree');

        return (
            <div tabIndex={-1} className="m-tree__collapse-all-btn" onClick={this.props.onCollapseAllItems}>
                {collapseAllLabel}
            </div>
        );
    }

    renderList() {
        const { items } = this.props;

        if (!items || !items.length) {
            return;
        }

        const { loadMoreSubitems, currentLocationId, subitemsLoadLimit, subitemsLimit, treeMaxDepth, afterItemToggle } = this.props;

        const attrs = {
            items,
            path: '',
            loadMoreSubitems,
            currentLocationId,
            subitemsLimit,
            subitemsLoadLimit,
            treeMaxDepth,
            afterItemToggle,
            isRoot: true,
        };

        return (
            <div className="m-tree__scrollable-wrapper">
                <List {...attrs} />
            </div>
        );
    }

    renderLoadingSpinner() {
        const { items } = this.props;

        if (items && items.length) {
            return;
        }

        return (
            <div className="m-tree__loading-spinner">
                <Icon name="spinner" extraClasses="ez-icon--medium ez-spin" />
            </div>
        );
    }

    render() {
        const { isResizing, containerWidth, resizedContainerWidth } = this.state;
        const width = isResizing ? resizedContainerWidth : containerWidth;
        const containerAttrs = { className: 'm-tree', ref: this._refTreeContainer };

        if (width) {
            containerAttrs.style = { width: `${width}px` };
        }

        return (
            <div {...containerAttrs}>
                {this.renderNoSubitemMessage()}
                {this.renderList()}
                {this.renderLoadingSpinner()}
                {this.renderCollapseAllBtn()}
                <div className="m-tree__resize-handler" onMouseDown={this.addWidthChangeListener} />
            </div>
        );
    }
}

ContentTree.propTypes = {
    items: PropTypes.array.isRequired,
    loadMoreSubitems: PropTypes.func.isRequired,
    currentLocationId: PropTypes.number.isRequired,
    subitemsLimit: PropTypes.number.isRequired,
    subitemsLoadLimit: PropTypes.number,
    treeMaxDepth: PropTypes.number.isRequired,
    afterItemToggle: PropTypes.func.isRequired,
    onCollapseAllItems: PropTypes.func.isRequired,
};
