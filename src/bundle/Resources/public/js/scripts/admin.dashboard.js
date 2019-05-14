(function(global, doc, eZ, Translator, React, ReactDOM) {
    const udw2Btn = doc.querySelector('#udw2-btn');
    const udw2Container = doc.querySelector('#udw2-container');
    const title = Translator.trans(/*@Desc("Browse content")*/ 'browse.title', {}, 'universal_discovery_widget');
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udw2Container);
    const handleOnConfirm = (items) => {
        console.log('onConfirm', items);

        closeUDW();
    };
    const openUDW = (event) => {
        event.preventDefault();

        const udwConfig = JSON.parse(event.currentTarget.dataset.udwConfig);
        const config = {
            title,
            onClose: closeUDW,
            tabs: [
                {
                    id: 'browse',
                    title: 'Browse',
                    panel: eZ.udwTabs.Browse,
                    active: false,
                    attrs: {
                        multiple: true,
                        selectedItemsLimit: 2,
                        startingLocationId: parseInt(event.currentTarget.dataset.startingLocationId, 10),
                        onConfirm: handleOnConfirm,
                        onCancel: closeUDW,
                    },
                },
                // {
                //     id: 'search',
                //     title: 'Search',
                //     panel: eZ.udwTabs.Search,
                //     active: true,
                //     attrs: {
                //         selectedItemsLimit: 2,
                //         searchResultsPerPage: 10,
                //         searchResultsLimit: 50,
                //         onConfirm: handleOnConfirm,
                //         onCancel: closeUDW,
                //     },
                // },
                {
                    id: 'create',
                    title: 'Create',
                    panel: eZ.udwTabs.Create,
                    active: true,
                    attrs: {
                        onConfirm: handleOnConfirm,
                        onCancel: closeUDW,
                        ...udwConfig,
                    },
                },
            ],
        };

        ReactDOM.render(React.createElement(eZ.modules.UDW, config), udw2Container);
    };

    udw2Btn.addEventListener('click', openUDW, false);
})(window, window.document, window.eZ, window.Translator, window.React, window.ReactDOM);
