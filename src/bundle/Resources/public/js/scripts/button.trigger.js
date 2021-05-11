document.addEventListener(
    'DOMContentLoaded',
    () => {
        const buttons = document.querySelectorAll('.ibexa-btn--trigger');
        const trigger = (event) => {
            event.preventDefault();

            const button = event.currentTarget;
            const triggerTargetElement = document.querySelector(button.dataset.click);

            triggerTargetElement.click();
        };

        buttons.forEach((button) => button.addEventListener('click', trigger, false));
    },
    false
);
