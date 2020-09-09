(function (global, doc) {
    const passwordInputNode = doc.querySelector('.ez-login__input--password');
    const viewIconNode = doc.querySelector('.ez-login__password-visibility-toggler .ez-icon--view');
    const viewHideIconNode = doc.querySelector('.ez-login__password-visibility-toggler .ez-icon--view-hide');

    doc.querySelector('.ez-login__password-visibility-toggler').addEventListener('click', (event) => {
        if (passwordInputNode) {
            const inputTypeToSet = passwordInputNode.type === 'password' ? 'text' : 'password';

            passwordInputNode.type = inputTypeToSet;
            viewIconNode.classList.toggle('d-none');
            viewHideIconNode.classList.toggle('d-none');
        }
    });
})(window, window.document);
