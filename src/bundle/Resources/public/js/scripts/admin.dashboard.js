(function(global, doc, eZ, Translator, React, ReactDOM) {
    const udw2Btn = doc.querySelector('#udw2-btn');
    const udw2Container = doc.querySelector('#udw2-container');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const title = Translator.trans(/*@Desc("Browse content")*/ 'browse.title', {}, 'universal_discovery_widget');
    const openUDW = (event) => {
        event.preventDefault();

        const config = {
            title,
            restInfo: { token, siteaccess },
            tabs: [
                {
                    id: 'browse',
                    title: 'Browse',
                    panel: eZ.udwTabs.Browse,
                    active: true,
                    attrs: {
                        multiple: true,
                        selectedItemsLimit: 2,
                        startingLocationId: parseInt(event.currentTarget.dataset.startingLocationId, 10),
                    },
                },
                {
                    id: 'search',
                    title: 'Search',
                    panel: eZ.udwTabs.Search,
                    active: false,
                    attrs: {},
                },
                {
                    id: 'create',
                    title: 'Create',
                    panel: eZ.udwTabs.Create,
                    active: false,
                    attrs: {},
                },
            ],
        };

        ReactDOM.render(React.createElement(eZ.modules.UDW, config), udw2Container);
    };

    udw2Btn.addEventListener('click', openUDW, false);
})(window, window.document, window.eZ, window.Translator, window.React, window.ReactDOM);
