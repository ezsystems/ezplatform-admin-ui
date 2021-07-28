import React, { Component } from 'react';
import PropTypes from 'prop-types';
import Icon from '../icon/icon';

const CLASS_NON_SCROLLABLE = 'ezs-non-scrollable';
const CLASS_MODAL_OPEN = 'modal-open';
const MODAL_CONFIG = {
    backdrop: 'static',
    keyboard: true,
};
const MODAL_SIZE_CLASS = {
    small: 'modal-sm',
    medium: '',
    large: 'modal-lg',
};

class Popup extends Component {
    constructor(props) {
        super(props);

        this._refModal = null;

        this.setModalRef = this.setModalRef.bind(this);
        this.onKeyUp = this.onKeyUp.bind(this);

        this.state = { isVisible: props.isVisible, isLoading: props.isLoading };
    }

    componentDidMount() {
        const { isVisible: show } = this.state;

        if (show) {
            const bootstrapModal = window.bootstrap.Modal.getOrCreateInstance(this._refModal, {
                ...MODAL_CONFIG,
                focus: this.props.hasFocus,
            });

            bootstrapModal.show();

            this.attachModalEventHandlers();
        }
    }

    componentDidUpdate() {
        const { isVisible: show } = this.state;

        const bootstrapModal = window.bootstrap.Modal.getOrCreateInstance(this._refModal, {
            ...MODAL_CONFIG,
            focus: this.props.hasFocus,
        });

        if (show) {
            bootstrapModal.show();
            this.attachModalEventHandlers();
        } else {
            bootstrapModal.hide();
        }
    }

    componentWillUnmount() {
        window.bootstrap.Modal.getOrCreateInstance(this._refModal).hide();
        document.body.classList.remove(CLASS_MODAL_OPEN, CLASS_NON_SCROLLABLE);
    }

    UNSAFE_componentWillReceiveProps({ isVisible, onConfigIframeLoad, isLoading }) {
        this.setState((state) => ({ ...state, isVisible, onConfigIframeLoad, isLoading }));
    }

    attachModalEventHandlers() {
        this._refModal.addEventListener('keyup', this.onKeyUp);
        this._refModal.addEventListener('hidden.bs.modal', this.props.onClose);
    }

    onKeyUp(event) {
        const { originalEvent } = event;
        const escKeyCode = 27;
        const escKeyPressed = originalEvent && (originalEvent.which === escKeyCode || originalEvent.keyCode === escKeyCode);

        if (escKeyPressed) {
            this.props.onClose();
        }
    }

    setModalRef(component) {
        this._refModal = component;
    }

    renderHeader() {
        const closeBtnLabel = Translator.trans(/*@Desc("Close")*/ 'popup.close.label', {}, 'universal_discovery_widget');

        return (
            <div className={'modal-header c-popup__header'}>
                {this.renderHeadline()}
                {this.renderCloseButton()}
            </div>
        );
    }

    renderCloseButton() {
        const closeBtnLabel = Translator.trans(/*@Desc("Close")*/ 'popup.close.label', {}, 'universal_discovery_widget');

        return (
            <button
                type="button"
                className="close c-popup__btn--close"
                data-bs-dismiss="modal"
                aria-label={closeBtnLabel}
                onClick={this.props.onClose}>
                <Icon name="discard" extraClasses="ibexa-icon--small" />
            </button>
        );
    }

    renderHeadline() {
        const { title } = this.props;

        if (!title) {
            return null;
        }

        return (
            <h3 className="modal-title c-popup__headline" title={this.props.title}>
                <span className="c-popup__title">{this.props.title}</span>
                {this.renderSubtitle()}
            </h3>
        );
    }

    renderSubtitle() {
        const { subtitle } = this.props;

        if (!subtitle) {
            return null;
        }

        return <span className="c-popup__subtitle">{subtitle}</span>;
    }

    renderFooter() {
        const { footerChildren } = this.props;

        if (!footerChildren) {
            return;
        }

        return <div className={'modal-footer c-popup__footer'}>{footerChildren}</div>;
    }

    render() {
        const { isVisible } = this.state;
        const { additionalClasses, size, noHeader } = this.props;
        const modalAttrs = {
            className: 'c-popup modal fade',
            ref: this.setModalRef,
            tabIndex: this.props.hasFocus ? -1 : undefined,
        };

        document.body.classList.toggle(CLASS_MODAL_OPEN, isVisible);
        document.body.classList.toggle(CLASS_NON_SCROLLABLE, isVisible);

        if (additionalClasses) {
            modalAttrs.className = `${modalAttrs.className} ${additionalClasses}`;
        }

        if (noHeader) {
            modalAttrs.className = `${modalAttrs.className} c-popup--no-header`;
        }

        return (
            <div {...modalAttrs}>
                <div className={`modal-dialog c-popup__dialog ${MODAL_SIZE_CLASS[size]}`} role="dialog">
                    <div className="modal-content c-popup__content">
                        {noHeader ? this.renderCloseButton() : this.renderHeader()}
                        <div className="modal-body c-popup__body">{this.props.children}</div>
                        {this.renderFooter()}
                    </div>
                </div>
            </div>
        );
    }
}

Popup.propTypes = {
    isVisible: PropTypes.bool,
    isLoading: PropTypes.bool,
    onClose: PropTypes.func.isRequired,
    onConfigIframeLoad: PropTypes.func,
    children: PropTypes.element.isRequired,
    title: PropTypes.string,
    subtitle: PropTypes.string,
    hasFocus: PropTypes.bool,
    additionalClasses: PropTypes.string,
    footerChildren: PropTypes.element,
    size: PropTypes.string,
    noHeader: PropTypes.bool,
};

Popup.defaultProps = {
    isVisible: false,
    isLoading: true,
    hasFocus: true,
    size: 'large',
    noHeader: false,
    onConfigIframeLoad: () => {},
};

export default Popup;
