const path = require('path');
const addJSEntries = require('./ez.js.config.js');
const addCSSEntries = require('./ez.css.config.js');

module.exports = (Encore) => {
    addJSEntries(Encore);
    addCSSEntries(Encore);
};
