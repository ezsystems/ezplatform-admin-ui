import React, { useLayoutEffect, useRef, useState } from 'react';
import PropTypes from 'prop-types';
import Icon from '../icon/icon';

const INITIAL_HEIGHT = 'initial';
const HEADER_HEIGHT = 35;

const TooltipPopupComponent = (props) => {
    const contentRef = useRef();
    const [maxHeight, setMaxHeight] = useState(INITIAL_HEIGHT);

    useLayoutEffect(() => {
        const { top, height } = contentRef.current.getBoundingClientRect();
        const topRounded = Math.round(top);

        if (topRounded < HEADER_HEIGHT) {
            setMaxHeight(height + topRounded - HEADER_HEIGHT);
        } else if (topRounded > HEADER_HEIGHT) {
            setMaxHeight(INITIAL_HEIGHT);
        }
    });

    const attrs = {
        className: 'c-tooltip-popup',
        hidden: !props.visible,
    };
    const contentStyle =
        maxHeight === INITIAL_HEIGHT
            ? {}
            : {
                maxHeight,
                overflowY: 'scroll',
            };
    const closeLabel = Translator.trans(/*@Desc("Close")*/ 'tooltip.close_label', {}, 'content');
    return (
        <div {...attrs}>
            <div className="c-tooltip-popup__header">
                <div className="c-tooltip-popup__title">{props.title}</div>
                <div
                    className="c-tooltip-popup__close"
                    title={closeLabel}
                    onClick={props.onClose}
                    tabIndex="-1"
                    data-tooltip-container-selector=".c-tooltip-popup__header">
                    <Icon name="discard" extraClasses="ibexa-icon--small" />
                </div>
            </div>
            <div className="c-tooltip-popup__content" ref={contentRef} style={contentStyle}>
                {props.children}
            </div>
            <div className="c-tooltip-popup__footer">
                <button className="btn ibexa-btn ibexa-btn--secondary" onClick={props.onClose}>
                    {closeLabel}
                </button>
            </div>
        </div>
    );
};

TooltipPopupComponent.propTypes = {
    title: PropTypes.string.isRequired,
    children: PropTypes.node.isRequired,
    visible: PropTypes.bool.isRequired,
    onClose: PropTypes.func,
};

TooltipPopupComponent.defaultProps = {
    onClose: () => { },
};

export default TooltipPopupComponent;
