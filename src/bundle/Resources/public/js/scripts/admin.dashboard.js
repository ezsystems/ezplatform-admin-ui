(function(global, doc, eZ, Translator, React, ReactDOM) {
    const udw2Btn = doc.querySelector('#udw2-btn');
    const udw2Container = doc.querySelector('#udw2-container');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const openUDW = (event) => {
        event.preventDefault();

        const config = {
            tabs: [
                {
                    id: 'browse',
                    title: 'Browse',
                    panel: eZ.udwTabs.Browse,
                    active: true,
                    attrs: {},
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
        const onConfirm = () => {
            console.log('onConfirm');
        };
        const onCancel = () => {
            console.log('onCancel');
        };
        const title = Translator.trans(/*@Desc("Browse content")*/ 'browse.title', {}, 'universal_discovery_widget');

        ReactDOM.render(
            React.createElement(
                eZ.modules.UDW,
                Object.assign(
                    {
                        onConfirm,
                        onCancel,
                        title,
                        multiple: false,
                        startingLocationId: parseInt(event.currentTarget.dataset.startingLocationId, 10),
                        restInfo: { token, siteaccess },
                    },
                    config
                )
            ),
            udw2Container
        );
    };

    console.log({ token, siteaccess });

    udw2Btn.addEventListener('click', openUDW, false);
})(window, window.document, window.eZ, window.Translator, window.React, window.ReactDOM);
