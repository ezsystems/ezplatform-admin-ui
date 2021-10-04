import React, { useContext } from 'react';

import {
    AllowConfirmationContext,
    ConfirmContext,
    CancelContext,
    SelectedLocationsContext,
 } from '../../universal.discovery.module';

const BottomMenu = () => {
    const onConfirm = useContext(ConfirmContext);
    const cancelUDW = useContext(CancelContext);
    const allowConfirmation = useContext(AllowConfirmationContext);
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const confirmLabel = Translator.trans(
        /*@Desc("Confirm")*/ 'selected_locations.confirm',
        {},
        'universal_discovery_widget'
    );
    const cancelLabel = Translator.trans(
        /*@Desc("Cancel")*/ 'selected_locations.cancel',
        {},
        'universal_discovery_widget'
    );
    const isConfirmDisabled = !allowConfirmation || selectedLocations.length === 0;

    return (
        <div className="c-bottom-menu">
            <span className="c-bottom-menu__confirm-btn-wrapper">
                <button
                    className="c-bottom-menu__confirm-btn btn ibexa-btn ibexa-btn--primary"
                    type="button"
                    onClick={() => onConfirm()}
                    disabled={isConfirmDisabled}
                >
                    {confirmLabel}
                </button>
            </span>
            <span className="c-bottom-menu__cancel-btn-wrapper">
                <button
                    className="c-bottom-menu__cancel-btn btn ibexa-btn ibexa-btn--secondary"
                    type="button"
                    onClick={cancelUDW}
                >
                    {cancelLabel}
                </button>
            </span>
        </div>
    );
};

export default BottomMenu;
