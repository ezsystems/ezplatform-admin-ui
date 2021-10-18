(function(global, doc, eZ) {
    const scrollOffset = 300;
    const formContainerNode = doc.querySelector('.ibexa-edit-content');
    const allSections = [...doc.querySelectorAll('.ibexa-anchor-navigation-sections__section')];
    const isVerticalScrollVisible = () => {
        const { scrollHeight, offsetHeight } = formContainerNode;

        return scrollHeight > offsetHeight;
    };
    const showSection = (sectionId) => {
        doc.querySelectorAll('.ibexa-anchor-navigation-menu__item-btn').forEach((btn) => {
            const { anchorTargetSectionId } = btn.dataset;

            btn.classList.toggle('ibexa-anchor-navigation-menu__item-btn--active', anchorTargetSectionId === sectionId);
        });

        doc.querySelectorAll('.ibexa-anchor-navigation-sections__section').forEach((section) => {
            const { anchorSectionId } = section.dataset;

            section.classList.toggle('ibexa-anchor-navigation-sections__section--active', anchorSectionId === sectionId);
        });
    };
    const navigateTo = (event) => {
        const { anchorTargetSectionId } = event.currentTarget.dataset;
        const targetSection = [...doc.querySelectorAll('.ibexa-anchor-navigation-sections__section')].find(
            (section) => section.dataset.anchorSectionId == anchorTargetSectionId
        );

        if (isVerticalScrollVisible()) {
            formContainerNode.scrollTo({
                top: targetSection.offsetTop,
                behavior: 'smooth',
            });
        } else {
            showSection(anchorTargetSectionId);
        }
    };

    doc.querySelectorAll('.ibexa-anchor-navigation-menu__item-btn').forEach((btn) => {
        btn.addEventListener('click', navigateTo, false);
    });

    if (formContainerNode && allSections.length) {
        formContainerNode.addEventListener('scroll', () => {
            const position = formContainerNode.scrollTop + scrollOffset;
            const activeSection = allSections.find((section) => {
                const start = section.offsetTop;
                const end = section.offsetHeight + section.offsetTop;

                return position > start && position < end;
            });

            if (activeSection) {
                const activeSectionId = activeSection.dataset.anchorSectionId;

                showSection(activeSectionId);
            }
        });
    }
})(window, window.document, window.eZ);
