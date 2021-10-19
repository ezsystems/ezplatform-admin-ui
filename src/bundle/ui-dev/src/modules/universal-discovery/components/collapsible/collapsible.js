import React, { useState } from 'react';

import { createCssClassNames } from '../../../common/helpers/css.class.names';

const Collapsible = ({ initiallyExpanded, title, children }) => {
    const [isExpanded, setIsExpanded] = useState(initiallyExpanded);
    const className = createCssClassNames({
        'c-filters__collapsible': true,
        'c-filters__collapsible--is-hidden': !isExpanded,
    });
    const toggleCollapsed = () => setIsExpanded((prevState) => !prevState);

    return (
        <div className={className}>
            <div className="c-filters__collapsible-title" onClick={toggleCollapsed}>
                {title}
            </div>
            <div className="c-filters__collapsible-content">
                {children}
            </div>
        </div>
    );
};

Collapsible.propTypes = {
    title: PropTypes.node.isRequired,
    children: PropTypes.node.isRequired,
    initiallyExpanded: PropTypes.bool,
}

Collapsible.defaultProps = {
    initiallyExpanded: false,
}

export default Collapsible;
