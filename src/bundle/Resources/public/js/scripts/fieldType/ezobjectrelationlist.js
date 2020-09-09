(function(global, doc, eZ, React, ReactDOM, Translator) {
    const CLASS_FIELD_SINGLE = 'ez-field-edit--ezobjectrelation';
    const SELECTOR_FIELD_MULTIPLE = '.ez-field-edit--ezobjectrelationlist';
    const SELECTOR_FIELD_SINGLE = '.ez-field-edit--ezobjectrelation';
    const SELECTOR_INPUT = '.ez-data-source__input';
    const SELECTOR_LABEL_WRAPPER = '.ez-field-edit__label-wrapper';
    const SELECTOR_BTN_ADD = '.ez-relations__table-action--create';
    const SELECTOR_ROW = '.ez-relations__item';
    const EVENT_CUSTOM = 'validateInput';

    class EzObjectRelationListValidator extends eZ.BaseFieldValidator {
        /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzObjectRelationListValidator
         */
        validateInput({ currentTarget }) {
            const isRequired = currentTarget.required;
            const isEmpty = !currentTarget.value.length;
            const hasCorrectValues = currentTarget.value.split(',').every((id) => !isNaN(parseInt(id, 10)));
            const fieldContainer = currentTarget.closest(SELECTOR_FIELD_MULTIPLE) || currentTarget.closest(SELECTOR_FIELD_SINGLE);
            const label = fieldContainer.querySelector('.ez-field-edit__label').innerHTML;
            const result = { isError: false };

            if (isRequired && isEmpty) {
                result.isError = true;
                result.errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);
            } else if (!isEmpty && !hasCorrectValues) {
                result.isError = true;
                result.errorMessage = eZ.errors.invalidValue.replace('{fieldName}', label);
            }

            return result;
        }
    }

    const singleObjectRelationFields = doc.querySelectorAll(SELECTOR_FIELD_SINGLE);

    if (singleObjectRelationFields.length) {
        console.warn('EzObjectRelation fieldtype is deprecated. Please, use EzObjectRelationList fieldtype instead.');
    }

    [...doc.querySelectorAll(SELECTOR_FIELD_MULTIPLE), ...singleObjectRelationFields].forEach((fieldContainer) => {
        const validator = new EzObjectRelationListValidator({
            classInvalid: 'is-invalid',
            fieldContainer,
            eventsMap: [
                {
                    selector: SELECTOR_INPUT,
                    eventName: 'blur',
                    callback: 'validateInput',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                },
                {
                    isValueValidator: false,
                    selector: SELECTOR_INPUT,
                    eventName: EVENT_CUSTOM,
                    callback: 'validateInput',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                },
            ],
        });
        const udwContainer = doc.getElementById('react-udw');
        const sourceInput = fieldContainer.querySelector(SELECTOR_INPUT);
        const relationsContainer = fieldContainer.querySelector('.ez-relations__list');
        const relationsWrapper = fieldContainer.querySelector('.ez-relations__wrapper');
        const relationsCTA = fieldContainer.querySelector('.ez-relations__cta');
        const addBtn = fieldContainer.querySelector(SELECTOR_BTN_ADD);
        const trashBtn = fieldContainer.querySelector('.ez-relations__table-action--remove');
        const isSingle = fieldContainer.classList.contains(CLASS_FIELD_SINGLE);
        const selectedItemsLimit = isSingle ? 1 : parseInt(relationsContainer.dataset.limit, 10);
        const startingLocationId =
            relationsContainer.dataset.defaultLocation !== '0' ? parseInt(relationsContainer.dataset.defaultLocation, 10) : null;
        const allowedContentTypes = relationsContainer.dataset.allowedContentTypes.split(',').filter((item) => item.length);
        const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
        const renderRows = (items) => items.forEach((...args) => relationsContainer.insertAdjacentHTML('beforeend', renderRow(...args)));
        const updateInputValue = (items) => {
            sourceInput.value = items.join();
            sourceInput.dispatchEvent(new CustomEvent(EVENT_CUSTOM));
        };
        const onConfirm = (items) => {
            items = excludeDuplicatedItems(items);

            renderRows(items);
            attachRowsEventHandlers();

            selectedItems = [...selectedItems, ...items.map((item) => item.ContentInfo.Content._id)];

            updateInputValue(selectedItems);
            closeUDW();
            updateFieldState();
            updateAddBtnState();
        };
        const canSelectContent = ({ item, itemsCount }, callback) => {
            const isAllowedContentType = allowedContentTypes.length
                ? allowedContentTypes.includes(item.ContentInfo.Content.ContentTypeInfo.identifier)
                : true;

            if (!isAllowedContentType) {
                return callback(false);
            }

            const isAlreadySelected = !!selectedItems.find((id) => id === item.ContentInfo.Content._id);

            if (!selectedItemsLimit) {
                return callback(!isAlreadySelected);
            }

            const canSelect = selectedItems.length + itemsCount < selectedItemsLimit && !isAlreadySelected;

            callback(canSelect);
        };
        const openUDW = (event) => {
            event.preventDefault();

            const config = JSON.parse(event.currentTarget.dataset.udwConfig);
            const limit = parseInt(event.currentTarget.dataset.limit, 10);
            const title =
                limit === 1
                    ? Translator.trans(
                          /*@Desc("Select a Content item")*/ 'ezobjectrelationlist.title.single',
                          {},
                          'universal_discovery_widget'
                      )
                    : Translator.trans(
                          /*@Desc("Select Content item(s)")*/ 'ezobjectrelationlist.title.multi',
                          {},
                          'universal_discovery_widget'
                      );

            ReactDOM.render(
                React.createElement(eZ.modules.UniversalDiscovery, {
                    onConfirm,
                    onCancel: closeUDW,
                    title,
                    multiple: isSingle ? false : selectedItemsLimit !== 1,
                    multipleItemsLimit: selectedItemsLimit,
                    startingLocationId,
                    canSelectContent,
                    ...config,
                }),
                udwContainer
            );
        };
        const excludeDuplicatedItems = (items) => {
            selectedItemsMap = items.reduce((total, item) => ({ ...total, [item.ContentInfo.Content._id]: item }), selectedItemsMap);

            return items.filter((item) => selectedItemsMap[item.ContentInfo.Content._id]);
        };
        const renderRow = (item, index) => {
            const { escapeHTML } = eZ.helpers.text;
            const { formatShortDateTime } = eZ.helpers.timezone;
            const contentTypeName = eZ.helpers.contentType.getContentTypeName(item.ContentInfo.Content.ContentTypeInfo.identifier);
            const contentName = escapeHTML(item.ContentInfo.Content.TranslatedName);
            const contentId = escapeHTML(item.ContentInfo.Content._id);

            return `
                <tr class="ez-relations__item" data-content-id="${contentId}">
                    <td><input class="ez-input ez-input--checkbox" type="checkbox" value="${contentId}" /></td>
                    <td class="ez-relations__item-name">${contentName}</td>
                    <td>${contentTypeName}</td>
                    <td>${formatShortDateTime(item.ContentInfo.Content.publishedDate)}</td>
                    <td colspan="2"><input class="ez-relations__order-input" type="number" value="${selectedItems.length +
                        index +
                        1}" /></td>
                </tr>
            `;
        };
        const updateFieldState = () => {
            const wrapperMethod = selectedItems.length ? 'removeAttribute' : 'setAttribute';
            const ctaMethod = selectedItems.length ? 'setAttribute' : 'removeAttribute';

            relationsWrapper[wrapperMethod]('hidden', true);
            relationsCTA[ctaMethod]('hidden', true);
        };
        const updateAddBtnState = () => {
            const methodName = !selectedItemsLimit || selectedItems.length < selectedItemsLimit ? 'removeAttribute' : 'setAttribute';

            addBtn[methodName]('disabled', true);
        };
        const updateTrashBtnState = (event) => {
            if ((!event.target.hasAttribute('type') || event.target.type !== 'checkbox') && event.currentTarget !== trashBtn) {
                return;
            }

            const anySelected = findCheckboxes().some((item) => item.checked === true);
            const methodName = anySelected ? 'removeAttribute' : 'setAttribute';

            trashBtn[methodName]('disabled', true);
        };
        const removeItem = (event) => {
            event.preventDefault();

            const removedItems = [];

            relationsContainer.querySelectorAll('input:checked').forEach((input) => {
                removedItems.push(parseInt(input.value, 10));

                input.closest('tr').remove();
            });

            selectedItems = selectedItems.filter((item) => !removedItems.includes(item));

            updateInputValue(selectedItems);
            updateFieldState();
            updateAddBtnState();
        };
        const findOrderInputs = () => {
            return [...relationsContainer.querySelectorAll('.ez-relations__order-input')];
        };
        const findCheckboxes = () => {
            return [...relationsContainer.querySelectorAll('[type="checkbox"]')];
        };
        const attachRowsEventHandlers = () => {
            const isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

            findOrderInputs().forEach((item) => {
                item.addEventListener('blur', updateSelectedItemsOrder, false);

                if (isFirefox) {
                    item.addEventListener('change', focusOnElement, false);
                }
            });
        };
        const focusOnElement = (event) => {
            if (doc.activeElement !== event.target) {
                event.target.focus();
            }
        };
        const emptyRelationsContainer = () => {
            while (relationsContainer.lastChild) {
                relationsContainer.removeChild(relationsContainer.lastChild);
            }
        };
        const updateSelectedItemsOrder = (event) => {
            event.preventDefault();

            const inputs = findOrderInputs().reduce((total, input) => {
                return [
                    ...total,
                    {
                        order: parseInt(input.value, 10),
                        row: input.closest(SELECTOR_ROW),
                    },
                ];
            }, []);

            inputs.sort((a, b) => a.order - b.order);

            const fragment = inputs.reduce((frag, item) => {
                frag.appendChild(item.row);

                return frag;
            }, doc.createDocumentFragment());

            emptyRelationsContainer();
            relationsContainer.appendChild(fragment);
            attachRowsEventHandlers();

            selectedItems = inputs.map((item) => parseInt(item.row.dataset.contentId, 10));
            updateInputValue(selectedItems);
        };
        let selectedItems = [...fieldContainer.querySelectorAll(SELECTOR_ROW)].map((row) => parseInt(row.dataset.contentId, 10));
        let selectedItemsMap = selectedItems.reduce((total, item) => ({ ...total, [item]: item }), {});

        updateAddBtnState();
        attachRowsEventHandlers();

        [
            ...fieldContainer.querySelectorAll(SELECTOR_BTN_ADD),
            ...fieldContainer.querySelectorAll('.ez-relations__cta-btn'),
        ].forEach((btn) => btn.addEventListener('click', openUDW, false));

        trashBtn.addEventListener('click', removeItem, false);
        trashBtn.addEventListener('click', updateTrashBtnState, false);
        relationsContainer.addEventListener('change', updateTrashBtnState, false);

        validator.init();

        eZ.addConfig('fieldTypeValidators', [validator], true);
    });
})(window, window.document, window.eZ, window.React, window.ReactDOM, window.Translator);
