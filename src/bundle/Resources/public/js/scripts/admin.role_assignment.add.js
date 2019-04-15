(function(global, doc, eZ, React, ReactDOM) {
    const udwContainer = doc.getElementById('react-udw');
    const limitationsRadio = doc.querySelectorAll('.ez-limitations__radio');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const selectSubtreeConfirm = (data) => {
        const selectedItems = data.reduce((total, item) => `${total}<li>${item.ContentInfo.Content.Name}</li>`, '');

        doc.querySelector('#role_assignment_create_locations').value = data.map((item) => item.id).join();
        doc.querySelector('.ez-limitations__selected-subtree').innerHTML = selectedItems;

        closeUDW();
    };
    const selectSubtree = (event) => {
        event.preventDefault();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);

        ReactDOM.render(
            React.createElement(
                eZ.modules.UniversalDiscovery,
                Object.assign(
                    {
                        onConfirm: selectSubtreeConfirm.bind(this),
                        onCancel: closeUDW,
                        multiple: true,
                        startingLocationId: eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
                        restInfo: { token, siteaccess },
                    },
                    config
                )
            ),
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

    const addContentToInput = (selectBtn, newlySelectedItems) => {
        const input = doc.querySelector(selectBtn.dataset.inputSelector);
        const newlySelectedContentIds = newlySelectedItems.map((item) => item.ContentInfo.Content._id).join(',');

        input.value = input.value ? `${input.value},${newlySelectedContentIds}` : newlySelectedContentIds;
    };
    const removeContentFromInput = (selectBtn, removedContentId) => {
        const input = doc.querySelector(selectBtn.dataset.inputSelector);
        const contentIdsWithoutRemoved = input.value.split(',').filter((contentId) => contentId !== removedContentId);

        input.value = contentIdsWithoutRemoved.join(',');
    };
    const addContentTags = (selectBtn, newlySelectedItems) => {
        const tagsList = doc.querySelector(selectBtn.dataset.selectedContentListSelector);
        const tagTemplate = selectBtn.dataset.tagTemplate;
        const fragment = doc.createDocumentFragment();

        newlySelectedItems.forEach((location) => {
            const { _id: contentId, Name: contentName } = location.ContentInfo.Content;
            const container = doc.createElement('ul');
            const renderedItem = tagTemplate.replace('{{ content_id }}', contentId).replace('{{ content_name }}', contentName);

            container.insertAdjacentHTML('beforeend', renderedItem);

            const listItemNode = container.querySelector('li');
            const tagNode = listItemNode.querySelector('.ez-tag');

            attachTagEventHandlers(selectBtn, tagNode);
            fragment.append(listItemNode);
        });

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
    const handleUdwConfirm = (selectBtn, newlySelectedItems) => {
        if (newlySelectedItems.length) {
            addContentToInput(selectBtn, newlySelectedItems);
            addContentTags(selectBtn, newlySelectedItems);
        }

        closeUDW();
    };
    const openUDW = (event) => {
        event.preventDefault();

        const selectBtn = event.currentTarget;
        const input = doc.querySelector(selectBtn.dataset.inputSelector);
        const selectedContentIds = input.value.split(',').map((idString) => parseInt(idString, 10));
        const config = JSON.parse(selectBtn.dataset.udwConfig);

        ReactDOM.render(
            React.createElement(
                eZ.modules.UniversalDiscovery,
                Object.assign(
                    {
                        onConfirm: handleUdwConfirm.bind(this, selectBtn),
                        onCancel: () => ReactDOM.unmountComponentAtNode(udwContainer),
                        startingLocationId: eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
                        title: selectBtn.dataset.universaldiscoveryTitle,
                        multiple: true,
                        restInfo: { token, siteaccess },
                        canSelectContent: ({ item }, callback) => {
                            const itemId = parseInt(item.ContentInfo.Content._id, 10);

                            callback(!selectedContentIds.includes(itemId));
                        },
                    },
                    config
                )
            ),
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
})(window, document, window.eZ, window.React, window.ReactDOM);
