(function(global, doc) {
    const passwordInputNode = doc.querySelector('.ibexa-login__input--password');
    const viewIconNode = doc.querySelector('.ibexa-login__password-visibility-toggler .ibexa-icon--view');
    const viewHideIconNode = doc.querySelector('.ibexa-login__password-visibility-toggler .ibexa-icon--view-hide');

    doc.querySelector('.ibexa-login__password-visibility-toggler').addEventListener('click', (event) => {
        if (passwordInputNode) {
            const inputTypeToSet = passwordInputNode.type === 'password' ? 'text' : 'password';

            passwordInputNode.type = inputTypeToSet;
            viewIconNode.classList.toggle('d-none');
            viewHideIconNode.classList.toggle('d-none');
        }
    });
})(window, window.document);
