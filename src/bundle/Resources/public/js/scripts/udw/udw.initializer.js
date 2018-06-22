(function(global, doc) {
    const eZ = (global.eZ = global.eZ || {});

    eZ.UdwInitializer = class UdwInitializer {
        constructor(openingButtons, getBaseConfig, beforeUdwOpen) {
            this.openingButtons = openingButtons;
            this.getBaseConfig = getBaseConfig;
            this.beforeUdwOpen = !!beforeUdwOpen ? beforeUdwOpen : () => {};
            this.udwConfig = {};

            this._onOpeningButtonClick = this._onOpeningButtonClick.bind(this);

            this._addOpeningButtonsListeners();
        }

        /**
         * Creates UdwInitializer instance which in turn creates opening buttons listeners.
         * On opening button click UDW is being opened.
         *
         * @method initialize
         * @param {Elements[]} openingButtons
         * @param {Function} getBaseConfig
         * @param {Function} beforeUdwOpen
         * @returns {UdwInitializer}
         * @memberof UdwInitializer
         */
        static initialize(openingButtons, getBaseConfig, beforeUdwOpen) {
            return new UdwInitializer(openingButtons, getBaseConfig, beforeUdwOpen);
        }

        _onOpeningButtonClick(event) {
            event.preventDefault();

            this.beforeUdwOpen(event);
            this._prepareUdwConfig(event);
            this._openUdw();
        }

        _getEventTargetUdwConfig(event) {
            return JSON.parse(event.currentTarget.dataset.udwConfig);
        }

        _addEventTargetUdwConfig(event) {
            const targetUdwConfig = this._getEventTargetUdwConfig(event);

            this.udwConfig = Object.assign(targetUdwConfig, this.udwConfig);
        }

        _setBaseConfig(event) {
            const baseUdwConfig = this.getBaseConfig(event);

            this.udwConfig = Object.assign(
                this.udwConfig,
                {
                    onCancel: () => {},
                },
                baseUdwConfig
            );
        }

        _prepareUdwConfig(event) {
            this._setBaseConfig(event);
            this._addEventTargetUdwConfig(event);
            this._wrapOnConfigFunctions();
            this._setRestInfoConfig();
        }

        _addOpeningButtonsListeners() {
            this.openingButtons.forEach((button) => button.addEventListener('click', this._onOpeningButtonClick, false));
        }

        _setRestInfoConfig() {
            const token = doc.querySelector('meta[name="CSRF-Token"]').content;
            const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;

            this.udwConfig.restInfo = { token, siteaccess };
        }

        _getUdwReactElement() {
            const React = global.React;
            const udwModule = eZ.modules.UniversalDiscovery;

            return React.createElement(udwModule, this.udwConfig);
        }

        _wrapWithClosing(fn) {
            if (!fn) {
                return null;
            }

            const self = this;

            return function() {
                self._closeUdw();

                fn.apply(null, arguments);
            };
        }

        _wrapOnConfigFunctions() {
            const onConfirm = this.udwConfig.onConfirm;
            const onCancel = this.udwConfig.onCancel;

            this.udwConfig.onConfirm = this._wrapWithClosing(onConfirm);
            this.udwConfig.onCancel = this._wrapWithClosing(onCancel);
        }

        _getUdwDomContainer() {
            return doc.getElementById('react-udw');
        }

        _openUdw() {
            const ReactDOM = global.ReactDOM;
            const udwReactElement = this._getUdwReactElement();
            const container = this._getUdwDomContainer();

            ReactDOM.render(udwReactElement, container);
        }

        _closeUdw() {
            const ReactDOM = global.ReactDOM;
            const container = this._getUdwDomContainer();

            ReactDOM.unmountComponentAtNode(container);
        }
    };
})(window, document);
