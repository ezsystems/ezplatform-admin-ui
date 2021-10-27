import React, { useContext } from 'react';

import {
    AllowConfirmationContext,
    ConfirmContext,
    CancelContext,
    SelectedLocationsContext,
 } from '../../universal.discovery.module';

const ActionsMenu = () => {
    const onConfirm = useContext(ConfirmContext);
    const cancelUDW = useContext(CancelContext);
    const allowConfirmation = useContext(AllowConfirmationContext);
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const confirmLabel = Translator.trans(
        /*@Desc("Confirm")*/ 'actions_menu.confirm',
        {},
        'universal_discovery_widget'
    );
    const cancelLabel = Translator.trans(
        /*@Desc("Cancel")*/ 'actions_menu.cancel',
        {},
        'universal_discovery_widget'
    );
    const isConfirmDisabled = !allowConfirmation || selectedLocations.length === 0;

    return (
        <div className="c-actions-menu">
            <span className="c-actions-menu__confirm-btn-wrapper">
                <button
                    className="c-actions-menu__confirm-btn btn ibexa-btn ibexa-btn--primary"
                    type="button"
                    onClick={() => onConfirm()}
                    disabled={isConfirmDisabled}
                >
                    {confirmLabel}
                </button>
            </span>
            <span className="c-actions-menu__cancel-btn-wrapper">
                <button
                    className="c-actions-menu__cancel-btn btn ibexa-btn ibexa-btn--secondary"
                    type="button"
                    onClick={cancelUDW}
                >
                    {cancelLabel}
                </button>
            </span>
        </div>
    );
};

export default ActionsMenu;
