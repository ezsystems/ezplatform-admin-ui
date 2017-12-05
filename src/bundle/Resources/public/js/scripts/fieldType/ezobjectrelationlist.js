(function (global, React, ReactDOM) {
    const CLASS_FIELD_SINGLE = 'ez-field-edit--ezobjectrelation';
    const SELECTOR_FIELD_MULTIPLE = '.ez-field-edit--ezobjectrelationlist';
    const SELECTOR_FIELD_SINGLE = '.ez-field-edit--ezobjectrelation';
    const SELECTOR_INPUT = '.ez-data-source__input';
    const SELECTOR_LABEL_WRAPPER = '.ez-field-edit__label-wrapper';
    const SELECTOR_BTN_ADD = '.ez-relations__table-action--create';
    const SELECTOR_ROW = '.ez-relations__item';
    const EVENT_CUSTOM = 'validateInput';

    class EzObjectRelationListValidator extends global.eZ.BaseFieldValidator {
        /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzObjectRelationListValidator
         */
        validateInput({currentTarget}) {
            const isRequired = currentTarget.required;
            const isEmpty = !currentTarget.value.length;
            const hasCorrectValues = currentTarget.value.split(',').every(id => !isNaN(parseInt(id, 10)));
            const fieldContainer = currentTarget.closest(SELECTOR_FIELD_MULTIPLE) || currentTarget.closest(SELECTOR_FIELD_SINGLE);
            const label = fieldContainer.querySelector('.ez-field-edit__label').innerHTML;
            const result = { isError: false };

            if (isRequired && isEmpty) {
                result.isError = true;
                result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);
            } else if (!isEmpty && !hasCorrectValues) {
                result.isError = true;
                result.errorMessage = global.eZ.errors.invalidValue.replace('{fieldName}', label);
            }

            return result;
        }
    }

    const singleObjectRelationFields = document.querySelectorAll(SELECTOR_FIELD_SINGLE);

    if (singleObjectRelationFields.length) {
        console.warn('EzObjectRelation fieldtype is deprecated. Please, use EzObjectRelationList fieldtype instead.');
    }

    [
        ...document.querySelectorAll(SELECTOR_FIELD_MULTIPLE),
        ...singleObjectRelationFields
    ].forEach(fieldContainer => {
        const validator = new EzObjectRelationListValidator({
            classInvalid: 'is-invalid',
            fieldContainer,
            eventsMap: [
                {
                    selector: SELECTOR_INPUT,
                    eventName: 'blur',
                    callback: 'validateInput',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER]
                },
                {
                    isValueValidator: false,
                    selector: SELECTOR_INPUT,
                    eventName: EVENT_CUSTOM,
                    callback: 'validateInput',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER]
                }
            ]
        });
        const udwContainer = document.getElementById('react-udw');
        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
        const sourceInput = fieldContainer.querySelector(SELECTOR_INPUT);
        const relationsContainer = fieldContainer.querySelector('.ez-relations__list');
        const relationsWrapper = fieldContainer.querySelector('.ez-relations__wrapper');
        const relationsCTA = fieldContainer.querySelector('.ez-relations__cta');
        const addBtn = fieldContainer.querySelector(SELECTOR_BTN_ADD);
        const isSingle = fieldContainer.classList.contains(CLASS_FIELD_SINGLE);
        const selectedItemsLimit = isSingle ? 1 : parseInt(relationsContainer.dataset.limit, 10);
        const startingLocation = relationsContainer.dataset.defaultLocation !== '0' ?
            parseInt(relationsContainer.dataset.defaultLocation, 10) : 1;
        const allowedContentTypes = relationsContainer.dataset.allowedContentTypes.split(',');
        const closeUDW = () => udwContainer.innerHTML = '';
        const renderRows = (items) => items.forEach((...args) => relationsContainer.insertAdjacentHTML('beforeend', renderRow(...args)));
        const updateInputValue = (items) => {
            sourceInput.value = items.join();
            sourceInput.dispatchEvent(new CustomEvent(EVENT_CUSTOM));
        };
        const onConfirm = (items) => {
            items = excludeDuplicatedItems(items);

            renderRows(items);
            attachRowsEventHandlers();

            selectedItems = [...selectedItems, ...items.map(item => item.ContentInfo.Content._id)];

            updateInputValue(selectedItems);
            closeUDW();
            updateFieldState();
            updateAddBtnState();
        };
        const canSelectContent = ({item, itemsCount}, callback) => {
            const isAllowedContentType = allowedContentTypes.length ?
                allowedContentTypes.includes(item.ContentInfo.Content.ContentTypeInfo.identifier) :
                true;

            if (!isAllowedContentType) {
                return callback(false);
            }

            if (!selectedItemsLimit) {
                return callback(true);
            }

            const canSelect = (selectedItems.length + itemsCount) < selectedItemsLimit &&
                !selectedItems.find(id => id === item.ContentInfo.Content._id);

            callback(canSelect);
        };
        const openUDW = (event) => {
            event.preventDefault();

            ReactDOM.render(React.createElement(global.eZ.modules.UniversalDiscovery, {
                onConfirm,
                onCancel: closeUDW,
                confirmLabel: 'Confirm selection',
                title: 'Select content',
                multiple: isSingle ? false : selectedItemsLimit !== 1,
                selectedItemsLimit,
                startingLocation,
                restInfo: { token, siteaccess },
                canSelectContent
            }), udwContainer);
        };
        const excludeDuplicatedItems = (items) => {
            selectedItemsMap = items.reduce((total, item) => Object.assign(
                {},
                total,
                { [item.ContentInfo.Content._id]: item }
            ), selectedItemsMap);

            return items.filter(item => selectedItemsMap[item.ContentInfo.Content._id]);
        };
        const renderRow = (item, index) => {
            return `
                <tr class="ez-relations__item" data-content-id="${item.ContentInfo.Content._id}">
                    <td><input type="checkbox" value="${item.ContentInfo.Content._id}" /></td>
                    <td>${item.ContentInfo.Content.Name}</td>
                    <td>${item.ContentInfo.Content.ContentTypeInfo.names.value[0]['#text']}</td>
                    <td>${(new Date(item.ContentInfo.Content.publishedDate)).toLocaleString()}</td>
                    <td><input class="ez-relations__order-input" type="number" value="${selectedItems.length + index + 1}" /></td>
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
            const methodName = selectedItems.length < selectedItemsLimit ? 'removeAttribute' : 'setAttribute';

            addBtn[methodName]('disabled', true);
        };
        const removeItem = (event) => {
            event.preventDefault();

            const removedItems = [];

            [...relationsContainer.querySelectorAll('input:checked')].forEach(input => {
                removedItems.push(parseInt(input.value, 10));

                input.closest('tr').remove();
            });

            selectedItems = selectedItems.filter(item => !removedItems.includes(item));

            updateInputValue(selectedItems);
            updateFieldState();
            updateAddBtnState();
        };
        const findOrderInputs = () => {
            return [...relationsContainer.querySelectorAll('.ez-relations__order-input')];
        };
        const attachRowsEventHandlers = () => {
            findOrderInputs().forEach(item => {
                item.addEventListener('blur', updateSelectedItemsOrder, false);
            });
        };
        const emptyRelationsContainer = () => {
            while (relationsContainer.lastChild) {
                relationsContainer.removeChild(relationsContainer.lastChild);
            }
        };
        const updateSelectedItemsOrder = (event) => {
            event.preventDefault();

            const inputs = findOrderInputs().reduce((total, input) => {
                return [...total, {
                    order: parseInt(input.value, 10),
                    row: input.closest(SELECTOR_ROW)
                }];
            }, []);

            inputs.sort((a, b) => a.order - b.order);

            const fragment = inputs.reduce((frag, item) => {
                frag.appendChild(item.row);

                return frag;
            }, document.createDocumentFragment());

            emptyRelationsContainer();
            relationsContainer.appendChild(fragment);
            attachRowsEventHandlers();

            selectedItems = inputs.map(item => parseInt(item.row.dataset.contentId, 10));
            updateInputValue(selectedItems);
        };
        let selectedItems = [...fieldContainer.querySelectorAll(SELECTOR_ROW)].map(row => parseInt(row.dataset.contentId, 10));
        let selectedItemsMap = selectedItems.reduce((total, item) => Object.assign({}, total, { [item]: item }), {});

        updateAddBtnState();

        [
            ...fieldContainer.querySelectorAll(SELECTOR_BTN_ADD),
            ...fieldContainer.querySelectorAll('.ez-relations__cta-btn')
        ].forEach(btn => btn.addEventListener('click', openUDW, false));

        fieldContainer.querySelector('.ez-relations__table-action--remove').addEventListener('click', removeItem, false);

        validator.init();

        global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
            [...global.eZ.fieldTypeValidators, validator] :
            [validator];
    });
})(window, window.React, window.ReactDOM);
