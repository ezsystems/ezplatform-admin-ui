;(function ($, window, document, undefined) {
    const requestData = function(action) {
        const request = new Request(action, {
            method: 'GET',
            mode: 'same-origin',
            credentials: 'same-origin'
        });

        fetch(request)
            .then(handleRequestResponse)
            .then(function(json) {
                initTreeView(json);
            })
            .catch(error => console.log('error:browsetree', error));
    };

    const requestChildren = function(action, par) {
        const request = new Request(action, {
            method: 'GET',
            mode: 'same-origin',
            credentials: 'same-origin'
        });

        fetch(request)
            .then(handleRequestResponse)
            .then(function(json) {
                if (json.children && json.children.length) {
                    $.each(json.children, function(i, node) {
                        $('#browsetree-view').jstree("create_node", par, node, 'last', false, false);
                    });

                    if (json.next) {
                        requestChildren(json.next, par);
                    }
                }
            })
            .catch(error => console.log('error:browsetree', error));
    };

    const initTreeView = function(json) {
        const browseTreeSideBar = document.querySelector('#browsetree-sidebar-widget');
        const browseTreeLocationId = browseTreeSideBar.dataset.locationid;

        $('#browsetree-view').jstree({
            'core' : {
                'multiple': false,
                'themes' : {
                    'dots' : false
                },
                'check_callback': true,
                'plugins:': ['types'],
                'data' : json
            }
        }).on('open_node.jstree', function (e, data) {
            if (data.node.children_d.length === 1 && !parseInt(data.node.children_d[0])) {
                $('#browsetree-view').jstree("delete_node", data.node.children_d[0]);
                requestChildren(data.node.a_attr.children, data.node);
            }
        }).on('select_node.jstree', function (e, data) {
            window.location.href = data.node.a_attr.href;
        });
    };

    const getClosest = function (elem, selector) {
        for ( ; elem && elem !== document; elem = elem.parentNode ) {
            if ( elem.matches( selector ) ) return elem;
        }
        return null;
    };

    const handleRequestResponse = response => {
        if (!response.ok) {
            throw Error(response.statusText);
        }

        return response.json();
    };

    const browseTreeSideBar = document.querySelector('#browsetree-sidebar-widget');
    if (browseTreeSideBar) {
        const browseTreeAction = browseTreeSideBar.dataset.action;
        requestData(browseTreeAction);
    }

})(jQuery, window, document);
