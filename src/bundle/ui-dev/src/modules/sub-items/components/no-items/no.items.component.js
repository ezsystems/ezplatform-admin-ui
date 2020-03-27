import React from 'react';

const NoItemsComponent = () => {
    const noItemsMessage = Translator.trans(/*@Desc("This location has no sub-items")*/ 'no_items.message', {}, 'sub_items');

    return <div className="c-no-items">{noItemsMessage}</div>;
};

export default NoItemsComponent;
