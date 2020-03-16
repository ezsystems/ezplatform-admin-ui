(function(global, doc, $) {
    // const hideButton = doc.querySelector('.ez-btn--hide');
    // const modal = doc.querySelector('#hide-content-modal');
    // const form = doc.querySelector('form[name="content_visibility_update"]');
    // const visiblity = doc.querySelector('#content_visibility_update_visible');

    // if (!hideButton) {
    //     return;
    // }

    // if (modal) {
    //     modal.querySelector('.btn-confirm').addEventListener('click', () => {
    //         visiblity.value = 0;
    //         //  form.submit();
    //     });
    // }

    // hideButton.addEventListener(
    //     'click',
    //     () => {
    //         if (modal) {
    //             $(modal).modal('show');
    //         } else {
    //             visiblity.value = 0;
    //             //   form.submit();
    //         }
    //     },
    //     false
    // );
    const scheduleHideWidget = doc.querySelector('.ez-extra-actions--schedule-hide');

    if (!scheduleHideWidget) {
        return;
    }

    const confirmButton = doc.querySelector('.ez-btn--hide-confirm');
    const modal = doc.querySelector('#hide-content-modal');
    const hideLaterRadio = scheduleHideWidget.querySelector('#later-id');
    const hideNowRadio = scheduleHideWidget.querySelector('#now-id');
    const pickerInput = scheduleHideWidget.querySelector('.ez-picker__input');
    const flatpickrInstance = pickerInput._flatpickr;
    const form = doc.querySelector('form[name="content_visibility_update"]');
    const visiblity = doc.querySelector('#content_visibility_update_visible');

    hideLaterRadio.addEventListener('change', () => {
        pickerInput.removeAttribute('disabled');
    });

    hideNowRadio.addEventListener('change', () => {
        pickerInput.setAttribute('disabled', true);
        flatpickrInstance.setDate(null, true);
    });

    if (modal) {
        modal.querySelector('.btn-confirm').addEventListener('click', () => {
            visiblity.value = 0; // to be removed when new form is created
            form.submit();
        });
    }

    confirmButton.addEventListener(
        'click',
        () => {
            if (modal) {
                $(modal).modal('show');
            } else {
                visiblity.value = 0; // to be removed when new form is created
                form.submit();
            }
        },
        false
    );
})(window, window.document, window.jQuery);
