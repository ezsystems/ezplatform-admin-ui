(function(global, doc, eZ, React, ReactDOM) {
    const udwContainer = doc.getElementById('react-udw');
    const limitationsRadio = doc.querySelectorAll('.ez-limitations__radio');
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const selectSubtreeConfirm = (data) => {
        const selectedItems = data.reduce((total, item) => `${total}<li>${item.ContentInfo.Content.TranslatedName}</li>`, '');

        doc.querySelector('#role_assignment_create_locations').value = data.map((item) => item.id).join();
        doc.querySelector('.ez-limitations__selected-subtree').innerHTML = selectedItems;

        closeUDW();
    };
    const selectSubtree = (event) => {
        event.preventDefault();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);

        ReactDOM.render(
            React.createElement(eZ.modules.UniversalDiscovery, {
                onConfirm: selectSubtreeConfirm.bind(this),
                onCancel: closeUDW,
                multiple: true,
                ...config,
            }),
            udwContainer
        );
    };
    const toggleDisabledState = () => {
        limitationsRadio.forEach((radio) => {
            const disableNode = doc.querySelector(radio.dataset.disableSelector);
            const methodName = radio.checked ? 'removeAttribute' : 'setAttribute';

            if (disableNode) {
                disableNode[methodName]('disabled', 'disabled');
            }
        });
    };

    doc.querySelector('.ez-btn--select-subtree').addEventListener('click', selectSubtree, false);
    limitationsRadio.forEach((radio) => radio.addEventListener('change', toggleDisabledState, false));

    const addContentToInput = (selectBtn, selectedItems) => {
        const input = doc.querySelector(selectBtn.dataset.inputSelector);
        const selectedContentIds = selectedItems.map((item) => item.ContentInfo.Content._id).join(',');

        input.value = selectedContentIds;
    };
    const removeContentFromInput = (selectBtn, removedContentId) => {
        const input = doc.querySelector(selectBtn.dataset.inputSelector);
        const contentIdsWithoutRemoved = input.value.split(',').filter((contentId) => contentId !== removedContentId);

        input.value = contentIdsWithoutRemoved.join(',');
    };
    const addContentTags = (selectBtn, selectedItems) => {
        const tagsList = doc.querySelector(selectBtn.dataset.selectedContentListSelector);
        const tagTemplate = selectBtn.dataset.tagTemplate;
        const fragment = doc.createDocumentFragment();

        selectedItems.forEach((location) => {
            const { _id: contentId, Name: contentName } = location.ContentInfo.Content;
            const container = doc.createElement('ul');
            const renderedItem = tagTemplate.replace('{{ content_id }}', contentId).replace('{{ content_name }}', contentName);

            container.insertAdjacentHTML('beforeend', renderedItem);

            const listItemNode = container.querySelector('li');
            const tagNode = listItemNode.querySelector('.ez-tag');

            attachTagEventHandlers(selectBtn, tagNode);
            fragment.append(listItemNode);
        });

        tagsList.innerHTML = '';
        tagsList.append(fragment);
    };
    const handleTagRemove = (selectBtn, tag) => {
        const removedContentId = tag.dataset.contentId;

        removeContentFromInput(selectBtn, removedContentId);
        tag.remove();
    };
    const attachTagEventHandlers = (selectBtn, tag) => {
        const removeTagBtn = tag.querySelector('.ez-tag__remove-btn');

        removeTagBtn.addEventListener('click', () => handleTagRemove(selectBtn, tag), false);
    };
    const handleUdwConfirm = (selectBtn, selectedItems) => {
        if (selectedItems.length) {
            addContentToInput(selectBtn, selectedItems);
            addContentTags(selectBtn, selectedItems);
            selectBtn.setAttribute('data-selected-locations', selectedItems.map((item) => item.id).join());
        }

        closeUDW();
    };
    const openUDW = (event) => {
        event.preventDefault();

        const selectBtn = event.currentTarget;
        const { selectedLocations } = selectBtn.dataset;
        const selectedLocationsIds = selectedLocations ? selectedLocations.split(',') : [];
        const config = JSON.parse(selectBtn.dataset.udwConfig);

        ReactDOM.render(
            React.createElement(eZ.modules.UniversalDiscovery, {
                onConfirm: handleUdwConfirm.bind(this, selectBtn),
                onCancel: () => ReactDOM.unmountComponentAtNode(udwContainer),
                title: selectBtn.dataset.universaldiscoveryTitle,
                multiple: true,
                selectedLocations: selectedLocationsIds,
                ...config,
            }),
            udwContainer
        );
    };
    const selectUsersBtn = doc.querySelector('#role_assignment_create_users__btn');
    const selectGroupsBtn = doc.querySelector('#role_assignment_create_groups__btn');

    [selectUsersBtn, selectGroupsBtn].forEach((selectBtn) => {
        selectBtn.addEventListener('click', openUDW, false);

        const tagsList = doc.querySelector(selectBtn.dataset.selectedContentListSelector);
        const tags = tagsList.querySelectorAll('.ez-tag');

        tags.forEach((tag) => {
            attachTagEventHandlers(selectBtn, tag);
        });
    });
})(window, window.document, window.eZ, window.React, window.ReactDOM);
