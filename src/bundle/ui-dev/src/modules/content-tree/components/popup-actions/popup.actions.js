import React, { useState, useEffect, useRef } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

import Icon from '../../../common/icon/icon';

const PopupActions = ({ listRef, options }) => {
    const containerRef = useRef();
    const containerItemsRef = useRef();
    const [isExpanded, setIsExpanded] = useState(false);
    const toggleExpanded = () => {
        setIsExpanded((prevState) => !prevState);
    };
    const getHeaderActions = () => {
        const { headerActions } = window.eZ.adminUiConfig.contentTreeWidget;
        const headerActionsArr = headerActions ? [...headerActions] : [];

        return headerActionsArr.sort((headerActionA, headerActionB) => {
            return headerActionB.priority - headerActionA.priority;
        });
    }
    const renderItem = (item) => {
        const Component = item.component;

        return (
            <li
                class="c-popup-actions__item"
                key={item.id}
                onClick={() => {
                    toggleExpanded();
                }}
            >
                <Component />
            </li>
        )
    }
    const renderItemsList = () => {
        const itemsStyles = {};
        const allOptions = [...options, ...getHeaderActions()];

        console.log(allOptions);

        if (containerRef.current) {
            const { left, top, height } = containerRef.current.getBoundingClientRect();

            itemsStyles.left = left;
            itemsStyles.top = top + height + 8;
        }

        return (
            <div class="c-popup-actions__items" style={itemsStyles} ref={containerItemsRef}>
                <ul class="c-popup-actions__items-list">
                    {allOptions.map(renderItem)}
                </ul>
            </div>
        )
    }

    useEffect(() => {
        if (!isExpanded) {
            return;
        }

        const onInteractionOutside = (event) => {
            if (containerRef.current.contains(event.target) || containerItemsRef.current.contains(event.target)) {
                return;
            }

            setIsExpanded(false);
        }

        document.body.addEventListener('click', onInteractionOutside, false);

        return () => {
            document.body.removeEventListener('click', onInteractionOutside, false);
        }
    }, [isExpanded]);

    return (
        <>
            <div
                className="c-popup-actions"
                ref={containerRef}
                onClick={toggleExpanded}
            >
                <Icon name="options" extraClasses="ibexa-icon--small" />
            </div>
            {isExpanded && ReactDOM.createPortal(
                renderItemsList(),
                listRef.current,
            )}
        </>
    );
};

PopupActions.propTypes = {
    listRef: PropTypes.object.isRequired,
    options: PropTypes.array.isRequired,
};

export default PopupActions;
