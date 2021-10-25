import React, { useContext, useState, useRef, useLayoutEffect } from 'react';
import PropTypes from 'prop-types';

import TopMenu from '../top-menu/top.menu';
import ActionsMenu from '../actions-menu/actions.menu';
import TabSelector from '../tab-selector/tab.selector';
import SelectedLocations from '../selected-locations/selected.locations';
import ContentCreateWidget from '../content-create-widget/content.create.widget';

import { SelectedLocationsContext, DropdownPortalRefContext } from '../../universal.discovery.module';

const Tab = ({ children, actionsDisabledMap }) => {
    const topBarRef = useRef();
    const bottomBarRef = useRef();
    const [contentHeight, setContentHeight] = useState('100%');
    const ContentMetaPreview = window.eZ.adminUiConfig.universalDiscoveryWidget.contentMetaPreview;
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const dropdownPortalRef = useContext(DropdownPortalRefContext);
    const selectedLocationsComponent = !!selectedLocations.length ? <SelectedLocations /> : null;
    const contentStyles = {
        height: contentHeight,
    };

    useLayoutEffect(() => {
        if (topBarRef.current && bottomBarRef.current) {
            const height = `calc(100% - ${topBarRef.current.offsetHeight + bottomBarRef.current.offsetHeight}px)`;

            setContentHeight(height);
        }
    });

    return (
        <div className="c-udw-tab">
            <div className="c-udw-tab__top-bar" ref={topBarRef}>
                <TopMenu actionsDisabledMap={actionsDisabledMap} />
            </div>
            <div className="c-udw-tab__content" style={contentStyles}>
                <div className="c-udw-tab__left-sidebar">
                    <ContentCreateWidget />
                    <TabSelector />
                </div>
                <div className="c-udw-tab__main">{children}</div>
                <div className="c-udw-tab__right-sidebar">
                    {ContentMetaPreview && <ContentMetaPreview />}
                    {selectedLocationsComponent}
                </div>
            </div>
            <div className="c-udw-tab__bottom-bar" ref={bottomBarRef}>
                <ActionsMenu />
            </div>
            <div class="c-udw-tab__dropdown-portal" ref={dropdownPortalRef} />
        </div>
    );
};

Tab.propTypes = {
    children: PropTypes.any.isRequired,
    actionsDisabledMap: PropTypes.object,
};

Tab.defaultProps = {
    actionsDisabledMap: {
        'content-create-button': false,
        'sort-switcher': false,
        'view-switcher': false,
    },
};

export default Tab;
