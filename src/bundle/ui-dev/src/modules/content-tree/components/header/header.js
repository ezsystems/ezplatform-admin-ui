import React from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '../../../common/helpers/css.class.names';
import Icon from '../../../common/icon/icon';

const Header = ({ isCollapsed, toggleCollapseTree, actions }) => {
    const headerTitle = Translator.trans(/*@Desc("Content tree")*/ 'content_tree.header', {}, 'content_tree');
    const renderCollapseButton = () => {
        const iconName = isCollapsed ? 'caret-next' : 'caret-back';
        const btnClassName = createCssClassNames({
            'ibexa-btn btn ibexa-btn--no-text ibexa-btn--tertiary': true,
            'c-header__expand-btn': isCollapsed,
        });

        return (
            <button
                type="button"
                className={btnClassName}
                onClick={toggleCollapseTree}
            >
                {isCollapsed && (
                    <Icon
                        name="content-tree"
                        extraClasses="ibexa-icon--medium"
                    />
                )}
                <Icon
                    name={iconName}
                    extraClasses="ibexa-icon--small"
                />
            </button>

        );
    };

    if (isCollapsed) {
        return renderCollapseButton();
    }

    return (
        <div className="c-header">
            {renderCollapseButton()}
            <div className="c-header__name">
                <Icon
                    name="content-tree"
                    extraClasses="ibexa-icon--small"
                />
                {headerTitle}
            </div>
            <div className="c-tb-header__options">
                {actions}
            </div>
        </div>
    );
};

Header.propTypes = {
    isCollapsed: PropTypes.bool.isRequired,
    toggleCollapseTree: PropTypes.func.isRequired,
    actions: PropTypes.array.isRequired,
};

export default Header;
