(function(global, doc, eZ, Leaflet) {
    const SELECTOR_FIELD = '.ibexa-field-edit--ezgmaplocation';
    const SELECTOR_ADDRESS_INPUT = '.ibexa-data-source__field--address .ibexa-data-source__input';
    const SELECTOR_LAT_FIELD = '.ibexa-data-source__field--latitude';
    const SELECTOR_LON_FIELD = '.ibexa-data-source__field--longitude';
    const SELECTOR_LAT_INPUT = '.ibexa-data-source__field--latitude .ibexa-data-source__input';
    const SELECTOR_LON_INPUT = '.ibexa-data-source__field--longitude .ibexa-data-source__input';
    const SELECTOR_ADDRESS_ERROR_NODE = '.ibexa-data-source__field--address';
    const SELECTOR_LAT_ERROR_NODE = '.ibexa-data-source__field--latitude';
    const SELECTOR_LON_ERROR_NODE = '.ibexa-data-source__field--longitude';
    const EVENT_BLUR = 'blur';
    const EVENT_KEYUP = 'keyup';
    const EVENT_CANCEL_ERRORS = 'ibexa-cancel-errors';
    const EVENT_ADDRESS_NOT_FOUND = 'ibexa-address-not-found';
    const POSITION_TYPE_LONGITUDE = 'longitude';
    const POSITION_TYPE_LATITUDE = 'latitude';
    const VALIDATE_LONGITUDE = 'validateLongitude';
    const VALIDATE_LATITUDE = 'validateLatitude';
    const VALIDATE_ADDRESS = 'validateAddress';

    class EzGMapLocationValidator extends eZ.BaseFieldValidator {
        /**
         * Validates latitude/longitude input value
         *
         * @method validateCoordInput
         * @param {HTMLElement} input
         * @param {Object} range of coord input
         * @returns {Object}
         */
        validateCoordInput(input, { min, max }) {
            const value = parseFloat(input.value.replace(',', '.'));
            const result = { isError: false };
            const label = input.closest('.ibexa-data-source__field').querySelector('.ibexa-data-source__label').innerHTML;
            const isNumber = !isNaN(value);
            const isInRange = value <= max && value >= min;

            if (!input.required && isNumber && isInRange) {
                return result;
            }

            if (isNumber && !isInRange) {
                result.isError = true;
                result.errorMessage = eZ.errors.outOfRangeValue
                    .replace('{fieldName}', label)
                    .replace('{min}', min)
                    .replace('{max}', max);

                return result;
            }

            if (input.required && !isNumber) {
                result.isError = true;
                result.errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);
            }

            return result;
        }

        /**
         * Validates longitude input value
         *
         * @method validateLongitude
         * @param {Event} event
         * @returns {Object}
         */
        validateLongitude(event) {
            const lonResult = this.validateCoordInput(event.currentTarget, { min: -180, max: 180 });

            if (lonResult.isError) {
                return lonResult;
            }

            const latInput = event.currentTarget.closest(SELECTOR_FIELD).querySelector(SELECTOR_LAT_INPUT);
            const latResult = this.validateCoordInput(latInput, { min: -90, max: 90 });
            const isNativeEvent = event.type && (event.type === EVENT_BLUR || event.type === EVENT_KEYUP);
            const allEmptyOrFilledResult = this.checkAllFieldsEmptyOrFilled(latInput, event.currentTarget);
            const invalidLatitude = allEmptyOrFilledResult.invalidInputType === POSITION_TYPE_LATITUDE;

            if (latResult.isError || (!isNativeEvent && invalidLatitude)) {
                return false;
            } else if (!isNativeEvent && allEmptyOrFilledResult.invalidInputType === POSITION_TYPE_LONGITUDE) {
                lonResult.isError = true;
                lonResult.errorMessage = allEmptyOrFilledResult.errorMessage;

                return lonResult;
            } else {
                return lonResult;
            }
        }

        /**
         * Validates latitude input value
         *
         * @method validateLatitude
         * @param {Event} event
         * @returns {Object}
         */
        validateLatitude(event) {
            const latResult = this.validateCoordInput(event.currentTarget, { min: -90, max: 90 });

            if (latResult.isError) {
                return latResult;
            }

            const lonInput = event.currentTarget.closest(SELECTOR_FIELD).querySelector(SELECTOR_LON_INPUT);
            const lonResult = this.validateCoordInput(lonInput, { min: -180, max: 180 });
            const isNativeEvent = event.type && (event.type === EVENT_BLUR || event.type === EVENT_KEYUP);
            const allEmptyOrFilledResult = this.checkAllFieldsEmptyOrFilled(event.currentTarget, lonInput);
            const invalidLongitude = allEmptyOrFilledResult.invalidInputType === POSITION_TYPE_LONGITUDE;

            if (lonResult.isError || (!isNativeEvent && invalidLongitude)) {
                return false;
            } else if (!isNativeEvent && allEmptyOrFilledResult.invalidInputType === POSITION_TYPE_LATITUDE) {
                latResult.isError = true;
                latResult.errorMessage = allEmptyOrFilledResult.errorMessage;

                return latResult;
            } else {
                return latResult;
            }
        }

        /**
         * Checks whether both longitude input field and latitude input field are filled or empty.
         *
         * @method checkAllFieldsEmptyOrFilled
         * @param {HTMLElement} latInput latitude input DOM node
         * @param {HTMLElement} lonInput longitude input DOM node
         * @returns {Object}
         */
        checkAllFieldsEmptyOrFilled(latInput, lonInput) {
            const lonInputFilled = lonInput.value.trim().length;
            const latInputFilled = latInput.value.trim().length;
            const lonInputFilledlatInputEmpty = lonInputFilled && !latInputFilled;
            const latInputFilledlonInputEmpty = !lonInputFilled && latInputFilled;

            let errorMessage = null;
            let invalidInputType = null;

            if (lonInputFilledlatInputEmpty) {
                errorMessage = eZ.errors.provideLatitudeValue;
                invalidInputType = POSITION_TYPE_LATITUDE;
            } else if (latInputFilledlonInputEmpty) {
                errorMessage = eZ.errors.provideLongitudeValue;
                invalidInputType = POSITION_TYPE_LONGITUDE;
            }

            return { isError: lonInputFilledlatInputEmpty || latInputFilledlonInputEmpty, invalidInputType, errorMessage };
        }

        /**
         * Validates longitude input value after clicking "Enter" key
         *
         * @method validateLongitudeOnEnter
         * @param {Event} event
         * @returns {Object}
         */
        validateLongitudeOnEnter(event) {
            event.preventDefault();
            event.stopPropagation();

            if (event.keyCode === 13) {
                return this.validateLongitude(event);
            }

            /**
             * If is not a Tab or Shift + Tab key set.
             *
             * When in the longitude field and after pressing the Tab or Shift + Tab key,
             * the keyup event fires on a latitude input field instead of a latitude input field.
             * It prevents such behaviour. The field will be validated on blur event.
             */
            if (event.keyCode !== 9 && event.keyCode !== 16) {
                return { isError: false };
            }
        }

        /**
         * Validates Latitude input value after clicking "Enter" key
         *
         * @method validateLatitudeOnEnter
         * @param {Event} event
         * @returns {Object}
         */
        validateLatitudeOnEnter(event) {
            event.preventDefault();
            event.stopPropagation();

            if (event.keyCode === 13) {
                return this.validateLatitude(event);
            }

            /**
             * If is not a Tab or Shift + Tab key set.
             *
             * When in the latitude field and after pressing the Tab or Shift + Tab key,
             * the keyup event fires on a longitude input field instead of a latitude input field.
             * It prevents such behaviour. The field will be validated on blur event.
             */
            if (event.keyCode !== 9 && event.keyCode !== 16) {
                return { isError: false };
            }
        }

        /**
         * Displays address not found error
         *
         * @method showNotFoundError
         * @returns {Object}
         */
        showNotFoundError() {
            return { isError: true, errorMessage: eZ.errors.addressNotFound };
        }

        /**
         * Validates the address input value.
         *
         * @method validateAddress
         * @param {Event} event
         * @returns {Object}
         */
        validateAddress(event) {
            const field = event.currentTarget.closest(SELECTOR_FIELD);
            const latInput = field.querySelector(SELECTOR_LAT_INPUT);
            const lonInput = field.querySelector(SELECTOR_LON_INPUT);

            if (!event.currentTarget.value.trim().length) {
                return { isError: false };
            }

            if (!latInput.value.trim().length || !lonInput.value.trim().length) {
                return { isError: true, errorMessage: eZ.errors.addressNotFound };
            }

            return { isError: false };
        }

        /**
         * Validates the lanitude input field on demand
         *
         * @method validateLatitudeOnDemand
         * @returns {Object} hash with 'result' and 'config' keys
         */
        validateLatitudeOnDemand() {
            const container = this.getFieldTypeContainer(doc);
            const latitudeInputConfig = this.eventsMap.find((eventConfig) => eventConfig.callback === VALIDATE_LATITUDE);

            return {
                result: this.validateLatitude({
                    currentTarget: container.querySelector(latitudeInputConfig.selector),
                }),
                config: latitudeInputConfig,
            };
        }

        /**
         * Validates the longitude input field on demand
         *
         * @method validateLongitudeOnDemand
         * @returns {Object} hash with 'result' and 'config' keys
         */
        validateLongitudeOnDemand() {
            const container = this.getFieldTypeContainer(doc);
            const longitudeInputConfig = this.eventsMap.find((eventConfig) => eventConfig.callback === VALIDATE_LONGITUDE);

            return {
                result: this.validateLongitude({
                    currentTarget: container.querySelector(longitudeInputConfig.selector),
                }),
                config: longitudeInputConfig,
            };
        }

        /**
         * Creates a hash with fields validation results and invalid state selectors
         *
         * @method buildCoordFieldsValidationHash
         * @param {Array} fieldsData
         * @returns {Object}
         */
        buildCoordFieldsValidationHash(fieldsData) {
            return {
                validationResults: fieldsData.map((field) => field.result),
                invalidStateSelectors: fieldsData.reduce((total, field) => [...total, ...field.config.invalidStateSelectors], []),
            };
        }

        /**
         * Validates the field
         *
         * @method validateField
         * @param {Object} config
         * @param {Event} event
         */
        validateField(config, event) {
            const validationResult = this[config.callback](event);

            if (!validationResult) {
                return;
            }

            this.toggleInvalidState(validationResult.isError, config, event.target);
            this.toggleErrorMessage(validationResult, config, event.target);

            if (validationResult.isError) {
                const errorMessage = Translator.trans(
                    /* @Desc("Area below needs correction") */ 'ezmaplocation.create.message.error',
                    {},
                    'fieldtypes_edit'
                );
                const allFieldsResult = { isError: true, errorMessage: errorMessage };

                config.errorNodeSelectors = [`${SELECTOR_FIELD} > .ibexa-form-error`];
                this.toggleInvalidState(true, config, event.target);
                this.toggleErrorMessage(allFieldsResult, config, event.target);
            }

            return validationResult;
        }
    }

    const validator = new EzGMapLocationValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                isValueValidator: false,
                selector: `${SELECTOR_FIELD} ${SELECTOR_ADDRESS_INPUT}`,
                eventName: EVENT_ADDRESS_NOT_FOUND,
                callback: 'showNotFoundError',
                errorNodeSelectors: [`${SELECTOR_FIELD} > .ibexa-form-error`],
            },
            {
                isValueValidator: false,
                selector: `${SELECTOR_FIELD} ${SELECTOR_ADDRESS_INPUT}`,
                eventName: EVENT_CANCEL_ERRORS,
                callback: 'cancelErrors',
                errorNodeSelectors: [`${SELECTOR_FIELD} > .ibexa-form-error`],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_ADDRESS_INPUT}`,
                eventName: 'checkValidity',
                callback: VALIDATE_ADDRESS,
                errorNodeSelectors: [SELECTOR_ADDRESS_ERROR_NODE],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_LON_INPUT}`,
                positionType: POSITION_TYPE_LONGITUDE,
                eventName: EVENT_BLUR,
                callback: VALIDATE_LONGITUDE,
                errorNodeSelectors: [`${SELECTOR_LON_ERROR_NODE} .ibexa-form-error`],
                invalidStateSelectors: [SELECTOR_LON_FIELD],
            },
            {
                isValueValidator: false,
                selector: `${SELECTOR_FIELD} ${SELECTOR_LON_INPUT}`,
                eventName: EVENT_KEYUP,
                callback: 'validateLongitudeOnEnter',
                errorNodeSelectors: [`${SELECTOR_LON_ERROR_NODE} .ibexa-form-error`],
                invalidStateSelectors: [SELECTOR_LON_FIELD],
            },
            {
                isValueValidator: false,
                selector: `${SELECTOR_FIELD} ${SELECTOR_LON_INPUT}`,
                eventName: EVENT_CANCEL_ERRORS,
                callback: 'cancelErrors',
                errorNodeSelectors: [`${SELECTOR_LON_ERROR_NODE} .ibexa-form-error`],
                invalidStateSelectors: [SELECTOR_LON_FIELD],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_LAT_INPUT}`,
                positionType: POSITION_TYPE_LATITUDE,
                eventName: EVENT_BLUR,
                callback: VALIDATE_LATITUDE,
                errorNodeSelectors: [`${SELECTOR_LAT_ERROR_NODE} .ibexa-form-error`],
                invalidStateSelectors: [SELECTOR_LAT_FIELD],
            },
            {
                isValueValidator: false,
                selector: `${SELECTOR_FIELD} ${SELECTOR_LAT_INPUT}`,
                eventName: EVENT_KEYUP,
                callback: 'validateLatitudeOnEnter',
                errorNodeSelectors: [`${SELECTOR_LAT_ERROR_NODE} .ibexa-form-error`],
                invalidStateSelectors: [SELECTOR_LAT_FIELD],
            },
            {
                isValueValidator: false,
                selector: `${SELECTOR_FIELD} ${SELECTOR_LAT_INPUT}`,
                eventName: EVENT_CANCEL_ERRORS,
                callback: 'cancelErrors',
                errorNodeSelectors: [`${SELECTOR_LAT_ERROR_NODE} .ibexa-form-error`],
                invalidStateSelectors: [SELECTOR_LAT_FIELD],
            },
        ],
    });

    validator.init();

    /**
     * Searches geo coords by a provided address
     *
     * @function searchByAddress
     * @param {String} value
     * @param {Function} foundCallback
     * @param {Function} notFoundCallback
     */
    const searchByAddress = (value, foundCallback, notFoundCallback) => {
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${global.encodeURI(value)}&zoom=15`)
            .then((response) => response.json())
            .then((locations) => {
                if (locations.length) {
                    foundCallback(locations[0].lat, locations[0].lon);
                } else {
                    notFoundCallback();
                }
            })
            .catch(eZ.helpers.notification.showErrorNotification);
    };

    /**
     * Disables default action of an event
     *
     * @function disableDefaultAction
     * @param {Event} event
     */
    const disableDefaultAction = (event) => event.preventDefault();

    /**
     * Prevents form submission
     *
     * @function preventFormSubmission
     * @param {Event} event
     */
    const preventFormSubmission = (event) => event.currentTarget.closest('form').addEventListener('submit', disableDefaultAction, false);

    /**
     * Enables form submission
     *
     * @function enableFormSubmission
     * @param {Event} event
     */
    const enableFormSubmission = (event) => event.currentTarget.closest('form').removeEventListener('submit', disableDefaultAction);

    /**
     * Corrects coord input field notation by replacing "," with "."
     *
     * @function correctNotation
     * @param {Event} event
     */
    const correctNotation = (event) => (event.currentTarget.value = event.currentTarget.value.replace(',', '.'));

    doc.querySelectorAll(SELECTOR_FIELD).forEach((field) => {
        const addressInput = field.querySelector(SELECTOR_ADDRESS_INPUT);
        const longitudeInput = field.querySelector(SELECTOR_LON_INPUT);
        const latitudeInput = field.querySelector(SELECTOR_LAT_INPUT);
        const areCoordsSet = !!longitudeInput.value.length && !!latitudeInput.value.length;
        const locateMeBtn = field.querySelector('.ibexa-data-source__locate-me .btn');
        const searchBtn = field.querySelector('.ibexa-btn--search-by-address');
        const mapConfig = {
            zoom: areCoordsSet ? 15 : 1,
            center: areCoordsSet ? [parseFloat(latitudeInput.value), parseFloat(longitudeInput.value)] : [0, 0],
        };
        const map = Leaflet.map(field.querySelector('.ibexa-data-source__map'), mapConfig);

        longitudeInput.value = longitudeInput.dataset.value.replace(',', '.');
        latitudeInput.value = latitudeInput.dataset.value.replace(',', '.');

        /**
         * Updates map state to show location with provided coordinates
         *
         * @function updateMapState
         * @param {Number} lat
         * @param {Number} lon
         */
        const updateMapState = (lat, lon) => {
            map.setView(Leaflet.latLng(lat, lon), 15);

            longitudeInput.value = lon;
            latitudeInput.value = lat;

            if (locationMarker) {
                map.removeLayer(locationMarker);
            }

            locationMarker = Leaflet.marker([lat, lon], {
                icon: new Leaflet.Icon.Default({
                    imagePath: '/bundles/ezplatformadminuiassets/vendors/leaflet/dist/images/',
                }),
            }).addTo(map);

            addressInput.dispatchEvent(new CustomEvent(EVENT_CANCEL_ERRORS));
            longitudeInput.dispatchEvent(new CustomEvent(EVENT_CANCEL_ERRORS));
            latitudeInput.dispatchEvent(new CustomEvent(EVENT_CANCEL_ERRORS));
        };

        /**
         * Displays address not found error
         *
         * @function showAddressNotFoundError
         */
        const showAddressNotFoundError = () => addressInput.dispatchEvent(new CustomEvent(EVENT_ADDRESS_NOT_FOUND));

        /**
         * Handles address input actions
         *
         * @function handleAddressInput
         * @param {Event} event
         */
        const handleAddressInput = (event) => {
            event.preventDefault();
            event.stopPropagation();

            if (event.keyCode === 13 || event.type === 'click') {
                searchByAddress(addressInput.value, updateMapState, showAddressNotFoundError);
            }
        };

        /**
         * Handles latitude input actions
         *
         * @function handleLatitudeInput
         * @param {Event} event
         */
        const handleLatitudeInput = (event) => {
            event.preventDefault();
            event.stopPropagation();

            if (event.keyCode !== 13) {
                return;
            }

            if (!longitudeInput.value.trim().length) {
                longitudeInput.dispatchEvent(new Event(EVENT_BLUR));

                return;
            }

            updateMapState(parseFloat(latitudeInput.value), parseFloat(longitudeInput.value));
        };

        /**
         * Handles longitude input actions
         *
         * @function handleLongitudeInput
         * @param {Event} event
         */
        const handleLongitudeInput = (event) => {
            event.preventDefault();
            event.stopPropagation();

            if (event.keyCode !== 13) {
                return;
            }

            if (!latitudeInput.value.trim().length) {
                latitudeInput.dispatchEvent(new Event(EVENT_BLUR));

                return;
            }

            updateMapState(parseFloat(latitudeInput.value), parseFloat(longitudeInput.value));
        };
        /**
         * Handles clicking on a map
         *
         * @function handleOnMapClick
         * @param {Event} event
         */
        const handleOnMapClick = (event) => {
            const latlng = event.latlng.wrap();

            updateMapState(latlng.lat, latlng.lng);
        };

        /**
         * IMPORTANT!
         * Requires a secure domain (HTTPS)
         *
         * Sets a current location on a map
         *
         * @function setCurrentLocation
         * @param {Event} event
         */
        const setCurrentLocation = (event) => {
            event.preventDefault();
            event.stopPropagation();

            navigator.geolocation.getCurrentPosition(
                (position) => updateMapState(position.coords.latitude, position.coords.longitude),
                (error) => eZ.helpers.notification.showErrorNotification(error)
            );
        };
        let locationMarker;

        Leaflet.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        if (areCoordsSet) {
            updateMapState(mapConfig.center[0], mapConfig.center[1]);
        }

        addressInput.addEventListener(EVENT_KEYUP, handleAddressInput, false);
        addressInput.addEventListener('focus', preventFormSubmission, false);
        addressInput.addEventListener(EVENT_BLUR, enableFormSubmission, false);
        searchBtn.addEventListener('click', handleAddressInput, false);
        longitudeInput.addEventListener(EVENT_KEYUP, handleLongitudeInput, false);
        longitudeInput.addEventListener(EVENT_BLUR, correctNotation, false);
        latitudeInput.addEventListener(EVENT_KEYUP, handleLatitudeInput, false);
        latitudeInput.addEventListener(EVENT_BLUR, correctNotation, false);
        map.on('click', handleOnMapClick);

        if (global.location.protocol === 'https:') {
            locateMeBtn.addEventListener('click', setCurrentLocation, false);
        } else {
            locateMeBtn.setAttribute('disabled', 'disabled');
        }
    });

    eZ.addConfig('fieldTypeValidators', [validator], true);
})(window, window.document, window.eZ, window.L);
