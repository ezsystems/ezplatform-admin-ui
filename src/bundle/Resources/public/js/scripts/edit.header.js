(function(global, doc, eZ) {
    const headerNode = doc.querySelector('.ibexa-edit-header');
    const contentNode = doc.querySelector('.ibexa-edit-content');

    const fitHeader = (event) => {
        const { scrollTop } = event.currentTarget;
        const shouldHeaderBeSlim = scrollTop > SCROLL_POSITION_TO_FIT;

        headerNode.classList.toggle('ibexa-edit-header--slim', shouldHeaderBeSlim);
    };

    console.log('xxxxxxx');
    contentNode.addEventListener('scroll', fitHeader, false);
})(window, window.document, window.eZ);