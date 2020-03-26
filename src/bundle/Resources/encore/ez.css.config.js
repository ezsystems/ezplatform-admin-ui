const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ezplatform-admin-ui-layout-css', [
        path.resolve(__dirname, '../public/scss/ezplatform-bootstrap.scss'),
        path.resolve(__dirname, '../public/scss/ezplatform.scss'),
        path.resolve(__dirname, '../public/scss/ui/ezplatform-modules.scss'),
        path.resolve('./vendor/ezsystems/ezplatform-admin-ui-assets/Resources/public/vendors/flatpickr/dist/flatpickr.min.css'),
    ])
        .addEntry('ezplatform-admin-ui-content-edit-parts-css', [
            path.resolve('./vendor/ezsystems/ezplatform-admin-ui-assets/Resources/public/vendors/leaflet/dist/leaflet.css'),
        ])
        .addEntry('ezplatform-admin-ui-location-view-css', [
            path.resolve('./vendor/ezsystems/ezplatform-admin-ui-assets/Resources/public/vendors/leaflet/dist/leaflet.css'),
        ])
        .addEntry('ezplatform-admin-ui-security-base-css', [
            path.resolve(__dirname, '../public/scss/ezplatform-bootstrap.scss'),
            path.resolve(__dirname, '../public/scss/ezplatform.scss'),
        ]);
};
