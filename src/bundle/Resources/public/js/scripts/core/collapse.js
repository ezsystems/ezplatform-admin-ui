(function(global, doc, eZ) {
    doc.querySelectorAll('.ibexa-collapse').forEach((collapseNode) => {
        const toggleButton = collapseNode.querySelector('.ibexa-collapse__toggle');
        const isCollapsed = toggleButton.classList.contains('collapsed');
        const initDraggableStatus = collapseNode.draggable ? collapseNode.draggable : false;
        
        console.log(initDraggableStatus);

        collapseNode.classList.toggle('ibexa-collapse--collapsed', isCollapsed);
        collapseNode.dataset.collapsed = isCollapsed ? true : false;

        collapseNode.addEventListener('hide.bs.collapse', (event) => {
            const target = event.currentTarget;

            collapseNode.classList.add('ibexa-collapse--collapsed');
            collapseNode.dataset.collapsed = true;
        }, false)


        collapseNode.addEventListener('show.bs.collapse', (event) => {
            collapseNode.classList.remove('ibexa-collapse--collapsed');
            collapseNode.dataset.collapsed = false;
        }, false);
    });
})(window, window.document);
