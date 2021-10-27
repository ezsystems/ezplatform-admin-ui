import React, { useContext, useMemo } from 'react';

import Icon from '../../../common/icon/icon';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import { TabsContext, TabsConfigContext, ActiveTabContext } from '../../universal.discovery.module';

const TabSelector = () => {
    const tabs = useContext(TabsContext);
    const tabsConfig = useContext(TabsConfigContext);
    const [activeTab, setActiveTab] = useContext(ActiveTabContext);
    const sortedTabs = useMemo(
        () =>
            tabs.sort((tabA, tabB) => {
                if (!tabsConfig[tabB.id] || !tabsConfig[tabA.id]) {
                    return 0;
                }

                return tabsConfig[tabB.id].priority - tabsConfig[tabA.id].priority;
            }),
        [tabs, tabsConfig]
    );

    return (
        <div className="c-tab-selector">
            {sortedTabs.map((tab) => {
                if (tab.isHiddenOnList || (tabsConfig[tab.id] && tabsConfig[tab.id].hidden)) {
                    return null;
                }

                const onClick = () => setActiveTab(tab.id);
                const className = createCssClassNames({
                    'c-tab-selector__item': true,
                    'c-tab-selector__item--selected': tab.id === activeTab,
                });

                return (
                    <div
                        className={className}
                        key={tab.id}
                        onClick={onClick}
                        title={tab.label}
                    >
                        <Icon customPath={tab.icon} extraClasses="ibexa-icon--small-medium" />
                    </div>
                );
            })}
        </div>
    );
};

export default TabSelector;
