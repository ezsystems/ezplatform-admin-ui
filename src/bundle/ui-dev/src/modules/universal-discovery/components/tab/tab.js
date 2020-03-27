import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import TopMenu from '../top-menu/top.menu';
import TabSelector from '../tab-selector/tab.selector';
import SelectedLocations from '../selected-locations/selected.locations';
import ContentCreateWidget from '../content-create-widget/content.create.widget';

import { SelectedLocationsContext } from '../../universal.discovery.module';

const Tab = ({ children, actionsDisabledMap }) => {
    const ContentMetaPreview = window.eZ.adminUiConfig.universalDiscoveryWidget.contentMetaPreview;
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const selectedLocationsComponent = !!selectedLocations.length ? <SelectedLocations /> : null;

    return (
        <div className="c-udw-tab">
            <div className="c-udw-tab__top-bar">
                <TopMenu actionsDisabledMap={actionsDisabledMap} />
            </div>
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
