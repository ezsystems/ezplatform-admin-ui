(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit--ezgmaplocation';
    const SELECTOR_ADDRESS_INPUT = '.ez-data-source__field--address .ez-data-source__input';
    const SELECTOR_LAT_INPUT = '.ez-data-source__field--latitude .ez-data-source__input';
    const SELECTOR_LON_INPUT = '.ez-data-source__field--longitude .ez-data-source__input';
    const SELECTOR_LABEL_WRAPPER = '.ez-field-edit__label-wrapper';

    class EzGMapLocationValidator extends global.eZ.BaseFieldValidator {
        /**
         * Validates latitude/longitude input value
         *
         * @method validateCoordInput
         * @param {HTMLElement} input
         * @param {Object} range of coord input
         * @returns {Boolean}
         */
        validateCoordInput(input, {min, max}) {
            const value = parseFloat(input.value.replace(',', '.'));
            const result = { isError: false };
            const label = input.closest('.ez-data-source__field').querySelector('.ez-data-source__label').innerHTML;
            const isNumber = !isNaN(value);
            const isInRange = (value <= max && value >= min);

            if (!input.required && isNumber && isInRange) {
                return result;
            }

            if (isNumber && !isInRange) {
                result.isError = true;
                result.errorMessage = window.eZ.errors.outOfRangeValue
                    .replace('{fieldName}', label)
                    .replace('{min}', min)
                    .replace('{max}', max);

                return result;
            }

            if (input.required && !isNumber) {
                result.isError = true;
                result.errorMessage = window.eZ.errors.emptyField.replace('{fieldName}', label);
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
            const lonResult = this.validateCoordInput(event.currentTarget, {min: -180, max: 180});

            if (lonResult.isError) {
                return lonResult;
            }

            const latInput = event.currentTarget.closest(SELECTOR_FIELD).querySelector(SELECTOR_LAT_INPUT);
            const latResult = this.validateCoordInput(latInput, {min: -90, max: 90});

            if (latResult.isError) {
                latInput.dispatchEvent(new Event('blur'));
            }

            return lonResult;
        }

        /**
         * Validates latitude input value
         *
         * @method validateLatitude
         * @param {Event} event
         * @returns {Object}
         */
        validateLatitude(event) {
            const latResult = this.validateCoordInput(event.currentTarget, {min: -90, max: 90});

            if (latResult.isError) {
                return latResult;
            }

            const lonInput = event.currentTarget.closest(SELECTOR_FIELD).querySelector(SELECTOR_LON_INPUT);
            const lonResult = this.validateCoordInput(lonInput, {min: -180, max: 180});

            if (lonResult.isError) {
                lonInput.dispatchEvent(new Event('blur'));
            }

            return latResult;
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
            return {
                isError: true,
                errorMessage: global.eZ.errors.addressNotFound
            };
        }
    }

    const validator = new EzGMapLocationValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [{
            selector: `${SELECTOR_FIELD} ${SELECTOR_LON_INPUT}`,
            eventName: 'blur',
            callback: 'validateLongitude',
            errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
            invalidStateSelectors: ['.ez-data-source__field--longitude']
        }, {
            isValueValidator: false,
            selector: `${SELECTOR_FIELD} ${SELECTOR_LON_INPUT}`,
            eventName: 'keyup',
            callback: 'validateLongitudeOnEnter',
            errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
            invalidStateSelectors: ['.ez-data-source__field--longitude']
        }, {
            isValueValidator: false,
            selector: `${SELECTOR_FIELD} ${SELECTOR_LON_INPUT}`,
            eventName: 'cancelErrors',
            callback: 'cancelErrors',
            errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
            invalidStateSelectors: ['.ez-data-source__field--longitude']
        }, {
            selector: `${SELECTOR_FIELD} ${SELECTOR_LAT_INPUT}`,
            eventName: 'blur',
            callback: 'validateLatitude',
            errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
            invalidStateSelectors: ['.ez-data-source__field--latitude']
        }, {
            isValueValidator: false,
            selector: `${SELECTOR_FIELD} ${SELECTOR_LAT_INPUT}`,
            eventName: 'keyup',
            callback: 'validateLatitudeOnEnter',
            errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
            invalidStateSelectors: ['.ez-data-source__field--latitude']
        }, {
            isValueValidator: false,
            selector: `${SELECTOR_FIELD} ${SELECTOR_LAT_INPUT}`,
            eventName: 'cancelErrors',
            callback: 'cancelErrors',
            errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
            invalidStateSelectors: ['.ez-data-source__field--latitude']
        }, {
            isValueValidator: false,
            selector: `${SELECTOR_FIELD} ${SELECTOR_ADDRESS_INPUT}`,
            eventName: 'addressNotFound',
            callback: 'showNotFoundError',
            errorNodeSelectors: [SELECTOR_LABEL_WRAPPER]
        }, {
            isValueValidator: false,
            selector: `${SELECTOR_FIELD} ${SELECTOR_ADDRESS_INPUT}`,
            eventName: 'cancelErrors',
            callback: 'cancelErrors',
            errorNodeSelectors: [SELECTOR_LABEL_WRAPPER]
        }],
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
        fetch(`http://nominatim.openstreetmap.org/search?format=json&q=${window.encodeURI(value)}&zoom=15`)
            .then(response => response.json())
            .then(locations => {
                if (locations.length) {
                    foundCallback(locations[0].lat, locations[0].lon);
                } else {
                    notFoundCallback();
                }
            })
            .catch(error => console.log('searchByAddress:error', error));
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
    const correctNotation = (event) => event.currentTarget.value = event.currentTarget.value.replace(',', '.');

    [...document.querySelectorAll(SELECTOR_FIELD)].forEach(field => {
        const addressInput = field.querySelector(SELECTOR_ADDRESS_INPUT);
        const longitudeInput = field.querySelector(SELECTOR_LON_INPUT);
        const latitudeInput = field.querySelector(SELECTOR_LAT_INPUT);
        const areCoordsSet = !!longitudeInput.value.length && !!latitudeInput.value.length;
        const locateMeBtn = field.querySelector('.ez-data-source__locate-me .btn');
        const searchBtn = field.querySelector('.btn--search-by-address');
        const mapConfig = {
            zoom: areCoordsSet ? 15 : 1,
            center: areCoordsSet ? [parseFloat(latitudeInput.value), parseFloat(longitudeInput.value)] : [0, 0]
        };
        const map = window.L.map(field.querySelector('.ez-data-source__map'), mapConfig);

        /**
         * Updates map state to show location with provided coordinates
         *
         * @function updateMapState
         * @param {Number} lat
         * @param {Number} lon
         */
        const updateMapState = (lat, lon) => {
            map.setView(window.L.latLng(lat, lon), 15);

            longitudeInput.value = lon;
            latitudeInput.value = lat;

            if (locationMarker) {
                map.removeLayer(locationMarker);
            }

            locationMarker = window.L.marker([lat, lon], {
                icon: new window.L.Icon.Default({
                    imagePath: '/bundles/ezplatformadminuiassets/vendors/leaflet/dist/images/'
                })
            }).addTo(map);

            addressInput.dispatchEvent(new CustomEvent('cancelErrors'));
            longitudeInput.dispatchEvent(new CustomEvent('cancelErrors'));
            latitudeInput.dispatchEvent(new CustomEvent('cancelErrors'));
        };

        /**
         * Displays address not found error
         *
         * @function showAddressNotFoundError
         */
        const showAddressNotFoundError = () => addressInput.dispatchEvent(new CustomEvent('addressNotFound'));

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
                longitudeInput.dispatchEvent(new Event('blur'));

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
                latitudeInput.dispatchEvent(new Event('blur'));

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
                (error) => console.log('setCurrentLocation:error', error)
            );
        };
        let locationMarker;

        window.L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        if (areCoordsSet) {
            updateMapState(mapConfig.center[0], mapConfig.center[1]);
        }

        addressInput.addEventListener('keyup', handleAddressInput, false);
        addressInput.addEventListener('focus', preventFormSubmission, false);
        addressInput.addEventListener('blur', enableFormSubmission, false);
        searchBtn.addEventListener('click', handleAddressInput, false);
        longitudeInput.addEventListener('keyup', handleLongitudeInput, false);
        longitudeInput.addEventListener('blur', correctNotation, false);
        latitudeInput.addEventListener('keyup', handleLatitudeInput, false);
        latitudeInput.addEventListener('blur', correctNotation, false);
        map.on('click', handleOnMapClick);

        if (window.location.protocol === 'https:') {
            locateMeBtn.addEventListener('click', setCurrentLocation, false);
        } else {
            locateMeBtn.setAttribute('disabled', 'disabled');
        }
    });

    global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
        [...global.eZ.fieldTypeValidators, validator] :
        [validator];
})(window);
