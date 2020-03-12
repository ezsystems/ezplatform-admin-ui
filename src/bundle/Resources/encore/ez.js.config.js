const path = require('path');
const fs = require('fs');
const translationsPath = path.resolve('./public/assets/translations/');
const fieldTypesPath = path.resolve(__dirname, '../public/js/scripts/fieldType/');
const layout = [
    path.resolve(__dirname, '../public/js/scripts/helpers/text.helper.js'),
    path.resolve(__dirname, '../public/js/scripts/helpers/request.helper.js'),
    path.resolve(__dirname, '../public/js/scripts/helpers/notification.helper.js'),
    path.resolve(__dirname, '../public/js/scripts/helpers/timezone.helper.js'),
    path.resolve(__dirname, '../public/js/scripts/helpers/content.type.helper.js'),
    path.resolve(__dirname, '../public/js/scripts/helpers/user.helper.js'),
    path.resolve(__dirname, '../public/js/scripts/helpers/tooltips.helper.js'),
    path.resolve(__dirname, '../public/js/scripts/admin.format.date.js'),
    path.resolve(__dirname, '../public/js/scripts/core/draggable.js'),
    path.resolve(__dirname, '../public/js/scripts/core/custom.dropdown.js'),
    path.resolve(__dirname, '../public/js/scripts/core/custom.tooltip.js'),
    path.resolve(__dirname, '../public/js/scripts/admin.notifications.js'),
    path.resolve(__dirname, '../public/js/scripts/button.trigger.js'),
    path.resolve(__dirname, '../public/js/scripts/button.prevent.default.js'),
    path.resolve(__dirname, '../public/js/scripts/udw/browse.js'),
    path.resolve(__dirname, '../public/js/scripts/admin.user.menu.js'),
    path.resolve(__dirname, '../public/js/scripts/admin.prevent.click.js'),
    path.resolve(__dirname, '../public/js/scripts/admin.picker.js'),
    path.resolve(__dirname, '../public/js/scripts/admin.notifications.modal.js'),
    path.resolve(__dirname, '../public/js/scripts/admin.location.add.translation.js'),
    path.resolve(__dirname, '../public/js/scripts/admin.form.autosubmit.js'),
];
const fieldTypes = [];

fs.readdirSync(translationsPath).forEach((file) => {
    if (file !== 'config.js' && path.extname(file) === '.js') {
        layout.push(path.resolve(translationsPath, file));
    }
});

fs.readdirSync(fieldTypesPath).forEach((file) => {
    if (path.extname(file) === '.js') {
        fieldTypes.push(path.resolve(fieldTypesPath, file));
    }
});

module.exports = (Encore) => {
    Encore.addEntry('ezplatform-admin-ui-layout-js', layout)
        .addEntry('ezplatform-admin-ui-bookmark-list-js', [
            path.resolve(__dirname, '../public/js/scripts/button.state.toggle.js'),
            path.resolve(__dirname, '../public/js/scripts/button.content.edit.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.version.edit.conflict.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.content.tree.js'),
        ])
        .addEntry('ezplatform-admin-ui-content-draft-list-js', [
            path.resolve(__dirname, '../public/js/scripts/button.state.toggle.js'),
            path.resolve(__dirname, '../public/js/scripts/button.content.edit.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.version.edit.conflict.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.content.tree.js'),
        ])
        .addEntry('ezplatform-admin-ui-content-type-create-js', [
            path.resolve(__dirname, '../public/js/scripts/admin.contenttype.selection.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.card.toggle.group.js'),
        ])
        .addEntry('ezplatform-admin-ui-content-type-edit-js', [
            path.resolve(__dirname, '../public/js/scripts/admin.contenttype.selection.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.card.toggle.group.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.contenttype.relation.default.location.js'),
        ])
        .addEntry('ezplatform-admin-ui-content-type-list-js', [path.resolve(__dirname, '../public/js/scripts/button.state.toggle.js')])
        .addEntry('ezplatform-admin-ui-content-type-view-js', [
            path.resolve(__dirname, '../public/js/scripts/admin.location.change.language.js'),
            path.resolve(__dirname, '../public/js/scripts/sidebar/extra.actions.js'),
            path.resolve(__dirname, '../public/js/scripts/sidebar/btn/contenttype.edit.js'),
            path.resolve(__dirname, '../public/js/scripts/button.state.toggle.js'),
        ])
        .addEntry('ezplatform-admin-ui-content-type-group-list-js', [
            path.resolve(__dirname, '../public/js/scripts/button.state.toggle.js'),
        ])
        .addEntry('ezplatform-admin-ui-language-list-js', [path.resolve(__dirname, '../public/js/scripts/button.state.toggle.js')])
        .addEntry('ezplatform-admin-ui-object-state-list-js', [path.resolve(__dirname, '../public/js/scripts/button.state.toggle.js')])
        .addEntry('ezplatform-admin-ui-object-state-group-list-js', [
            path.resolve(__dirname, '../public/js/scripts/button.state.toggle.js'),
        ])
        .addEntry('ezplatform-admin-ui-policy-create-with-limitation-js', [
            path.resolve(__dirname, '../public/js/scripts/admin.limitation.pick.js'),
        ])
        .addEntry('ezplatform-admin-ui-policy-edit-js', [path.resolve(__dirname, '../public/js/scripts/admin.limitation.pick.js')])
        .addEntry('ezplatform-admin-ui-role-list-js', [path.resolve(__dirname, '../public/js/scripts/button.state.toggle.js')])
        .addEntry('ezplatform-admin-ui-role-view-js', [
            path.resolve(__dirname, '../public/js/scripts/button.state.toggle.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.location.tab.js'),
        ])
        .addEntry('ezplatform-admin-ui-role-assignment-create-js', [
            path.resolve(__dirname, '../public/js/scripts/admin.role_assignment.add.js'),
        ])
        .addEntry('ezplatform-admin-ui-search-js', [
            path.resolve(__dirname, '../public/js/scripts/button.content.edit.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.search.filters.js'),
            path.resolve(__dirname, '../public/js/scripts/udw/select.location.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.content.tree.js'),
        ])
        .addEntry('ezplatform-admin-ui-section-list-js', [
            path.resolve(__dirname, '../public/js/scripts/admin.section.list.js'),
            path.resolve(__dirname, '../public/js/scripts/button.state.toggle.js'),
        ])
        .addEntry('ezplatform-admin-ui-section-view-js', [path.resolve(__dirname, '../public/js/scripts/admin.section.view.js')])
        .addEntry('ezplatform-admin-ui-trash-list-js', [
            path.resolve(__dirname, '../public/js/scripts/admin.trash.list.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.content.tree.js'),
        ])
        .addEntry('ezplatform-admin-ui-content-preview-js', [path.resolve(__dirname, '../public/js/scripts/admin.preview.js')])
        .addEntry('ezplatform-admin-ui-location-view-js', [
            path.resolve(__dirname, '../public/js/scripts/admin.location.change.language.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.content.tree.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.location.view.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.location.tab.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.location.adaptive.tabs.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.location.visibility.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.location.update.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.location.tooglecontentpreview.js'),
            path.resolve(__dirname, '../public/js/scripts/button.content.edit.js'),
            path.resolve(__dirname, '../public/js/scripts/udw/move.js'),
            path.resolve(__dirname, '../public/js/scripts/udw/copy.js'),
            path.resolve(__dirname, '../public/js/scripts/udw/swap.js'),
            path.resolve(__dirname, '../public/js/scripts/udw/copy_subtree.js'),
            path.resolve(__dirname, '../public/js/scripts/udw/locations.tab.js'),
            path.resolve(__dirname, '../public/js/scripts/sidebar/extra.actions.js'),
            path.resolve(__dirname, '../public/js/scripts/sidebar/btn/location.edit.js'),
            path.resolve(__dirname, '../public/js/scripts/sidebar/btn/user.edit.js'),
            path.resolve(__dirname, '../public/js/scripts/sidebar/btn/location.create.js'),
            path.resolve(__dirname, '../public/js/scripts/sidebar/instant.filter.js'),
            path.resolve('./vendor/ezsystems/ezplatform-admin-ui-assets/Resources/public/vendors/leaflet/dist/leaflet.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.location.load.map.js'),
            path.resolve(__dirname, '../public/js/scripts/sidebar/btn/content.edit.js'),
            path.resolve(__dirname, '../public/js/scripts/sidebar/btn/content.hide.js'),
            path.resolve(__dirname, '../public/js/scripts/sidebar/btn/content.reveal.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.location.add.custom_url.js'),
            path.resolve(__dirname, '../public/js/scripts/button.state.toggle.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.version.edit.conflict.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.location.bookmark.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.main.translation.update.js'),
        ])
        .addEntry('ezplatform-admin-ui-modal-location-trash-js', [path.resolve(__dirname, '../public/js/scripts/admin.trash.js')])
        .addEntry('ezplatform-admin-ui-modal-location-trash-container-js', [
            path.resolve(__dirname, '../public/js/scripts/button.state.checkbox.toggle.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.trash.container.js'),
        ])
        .addEntry('ezplatform-admin-ui-modal-location-trash-single-asset-js', [
            path.resolve(__dirname, '../public/js/scripts/button.state.radio.toggle.js'),
        ])
        .addEntry('ezplatform-admin-ui-dashboard-js', [
            path.resolve(__dirname, '../public/js/scripts/udw/browse.js'),
            path.resolve(__dirname, '../public/js/scripts/cotf/create.js'),
            path.resolve(__dirname, '../public/js/scripts/button.content.edit.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.version.edit.conflict.js'),
        ])
        .addEntry('ezplatform-admin-ui-link-manager-list-js', [path.resolve(__dirname, '../public/js/scripts/admin.linkmanager.list.js')])
        .addEntry('ezplatform-admin-ui-link-manager-view-js', [path.resolve(__dirname, '../public/js/scripts/button.content.edit.js')])
        .addEntry('ezplatform-admin-ui-change-user-password-js', [path.resolve(__dirname, '../public/js/scripts/user_password.change.js')])
        .addEntry('ezplatform-admin-ui-content-edit-parts-js', [
            path.resolve('./vendor/ezsystems/ezplatform-admin-ui-assets/Resources/public/vendors/leaflet/dist/leaflet.js'),
            path.resolve(__dirname, '../public/js/scripts/admin.content.edit.js'),
            path.resolve(__dirname, '../public/js/scripts/fieldType/base/base-field.js'),
            path.resolve(__dirname, '../public/js/scripts/fieldType/base/base-file-field.js'),
            path.resolve(__dirname, '../public/js/scripts/fieldType/base/base-preview-field.js'),
            ...fieldTypes,
            path.resolve(__dirname, '../public/js/scripts/sidebar/extra.actions.js'),
        ])
        .addEntry('ezplatform-admin-ui-settings-datetime-format-update-js', [
            path.resolve(__dirname, '../public/js/scripts/admin.settings.datetimeformat.update.js'),
        ]);
};
