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
        this.scrollTimeout = null;
        this.scrollPositionSet = false;

        this.state = {
            resizeStartPositionX: 0,
            containerWidth: this.getConfig('width'),
            resizedContainerWidth: 0,
            isResizing: false,
        };
    }

    componentWillUnmount() {
        this.clearDocumentResizingListeners();
    }

    componentDidMount() {
        this.containerSrollRef.addEventListener('scroll', (event) => {
            clearTimeout(this.scrollTimeout);

            this.scrollTimeout = setTimeout(
                (scrollTop) => {
                    this.saveConfig('scrollTop', scrollTop);
                },
                50,
                event.currentTarget.scrollTop
            );
        });
    }

    componentDidUpdate(prevState) {
        if (this.state.containerWidth !== prevState.containerWidth) {
            this.saveConfig('width', this.state.containerWidth);

            document.body.dispatchEvent(new CustomEvent('ez-content-tree-resized'));
        }

        if (this.props.items.length && !this.scrollPositionSet) {
            this.scrollPositionSet = true;

            this.containerSrollRef.scrollTo(0, this.getConfig('scrollTop'));
        }
    }

    saveConfig(id, value) {
        const data = JSON.parse(window.localStorage.getItem('ez-content-tree-config') || '{}');

        data[id] = value;

        window.localStorage.setItem('ez-content-tree-config', JSON.stringify(data));
    }

    getConfig(id) {
        const data = JSON.parse(window.localStorage.getItem('ez-content-tree-config') || '{}');

        return data[id];
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

    renderCollapseAllBtn() {
        const collapseAllLabel = Translator.trans(/*@Desc("Collapse all")*/ 'collapse_all', {}, 'content_tree');

        return (
            <div tabIndex={-1} className="m-tree__collapse-all-btn" onClick={this.props.onCollapseAllItems}>
                {collapseAllLabel}
            </div>
        );
    }

    renderList() {
        const {
            items,
            loadMoreSubitems,
            currentLocationId,
            onClickItem,
            subitemsLoadLimit,
            subitemsLimit,
            treeMaxDepth,
            afterItemToggle,
        } = this.props;

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
            onClickItem,
        };

        return (
            <div className="m-tree__scrollable-wrapper" ref={(ref) => (this.containerSrollRef = ref)}>
                {!items || !items.length ? null : <List {...attrs} />}
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
    onClickItem: PropTypes.func,
};
