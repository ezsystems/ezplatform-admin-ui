import React, { useState, useContext } from 'react';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import { SelectedContentTypesContext } from '../search/search';

const ContentTypeSelector = () => {
    const { contentTypes: contentTypesMap } = window.eZ.adminUiConfig;
    const [selectedContentTypes, dispatchSelectedContentTypesAction] = useContext(SelectedContentTypesContext);
    const [collapsedGroups, setCollapsedGroups] = useState(() => {
        return Object.keys(contentTypesMap).reduce((collapsedGroups, contentTypeGroup, index) => {
            return { ...collapsedGroups, [contentTypeGroup]: !!index };
        }, {});
    });
    const toggleCollapsed = ({ nativeEvent }) => {
        const contentTypeGroup = nativeEvent.target.dataset.id;

        setCollapsedGroups((prevState) => ({ ...prevState, [contentTypeGroup]: !prevState[contentTypeGroup] }));
    };
    const handleContentTypeSelect = ({ nativeEvent }) => {
        const contentTypeIdentifier = nativeEvent.target.dataset.contentTypeIdentifier;
        const action = { contentTypeIdentifier };

        action.type = selectedContentTypes.includes(contentTypeIdentifier) ? 'REMOVE_CONTENT_TYPE' : 'ADD_CONTENT_TYPE';

        dispatchSelectedContentTypesAction(action);
    };

    return (
        <div className="ez-content-type-selector c-content-type-selector">
            {Object.entries(contentTypesMap).map(([contentTypeGroup, contentTypes]) => {
                const groupSelectorClassName = createCssClassNames({
                    'ez-content-type-selector__group': true,
                    'ez-content-type-selector__group--collapsed': collapsedGroups[contentTypeGroup],
                });

                return (
                    <div key={contentTypeGroup} className={groupSelectorClassName}>
                        <span className="ez-content-type-selector__group-title" data-id={contentTypeGroup} onClick={toggleCollapsed}>
                            {contentTypeGroup}
                        </span>
                        <ul className="ez-content-type-selector__list">
                            {contentTypes.map((contentType) => {

                                return (
                                    <li key={contentType.identifier} className="ez-content-type-selector__item">
                                        <div className="form-check form-check-inline">
                                            <input
                                                type="checkbox"
                                                id={`ez-search-content-type-${contentType.identifier}`}
                                                className="ez-input ez-input--checkbox"
                                                value={contentType.identifier}
                                                data-content-type-identifier={contentType.identifier}
                                                onChange={handleContentTypeSelect}
                                                checked={selectedContentTypes.includes(contentType.identifier)}
                                            />
                                            <label
                                                className="checkbox-inline form-check-label"
                                                htmlFor={`ez-search-content-type-${contentType.identifier}`}>
                                                {contentType.name}
                                            </label>
                                        </div>
                                    </li>
                                );
                            })}
                        </ul>
                    </div>
                );
            })}
        </div>
    );
};

export default ContentTypeSelector;
