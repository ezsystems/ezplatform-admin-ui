(function(global, doc, eZ) {
    const escapeHTML = (string) => {
        const stringTempNode = doc.createElement('div');

        stringTempNode.appendChild(doc.createTextNode(string));

        return stringTempNode.innerHTML;
    };

    eZ.addConfig('helpers.text', {
        escapeHTML,
    });
})(window, window.document, window.eZ);
