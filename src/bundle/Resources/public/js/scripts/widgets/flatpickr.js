import flatpickrLanguages
    from '../../../../../../../../../../public/bundles/ezplatformadminuiassets/vendors/flatpickr/dist/l10n';

(function (global, doc, eZ, flatpickr) {
    const { backOfficeLanguage } = eZ.adminUiConfig;
    const flatpickrLanguage = flatpickrLanguages[backOfficeLanguage] ?? flatpickrLanguages.default;

    flatpickr.localize(flatpickrLanguage);

})(global, doc, window.eZ, window.flatpickr);
