(function(doc, React, ReactDOM, eZ) {
    const contentTreeContainer = doc.querySelector('.ez-content-tree-container');

    ReactDOM.render(React.createElement(eZ.modules.ContentTree, {}), contentTreeContainer);
})(window.document, window.React, window.ReactDOM, window.eZ);
