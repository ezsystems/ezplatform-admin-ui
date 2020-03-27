import React from 'react';
import PropTypes from 'prop-types';

const ProgressBarComponent = (props) => {
    return (
        <div className="c-progress-bar">
            <div className="c-progress-bar__value" style={{ width: `${props.progress}%` }} />
            <div className="c-progress-bar__label">{`${props.progress}%`}</div>
            <div className="c-progress-bar__uploaded">
                {props.uploaded} of {props.total}
            </div>
        </div>
    );
};

ProgressBarComponent.propTypes = {
    progress: PropTypes.number.isRequired,
    uploaded: PropTypes.string.isRequired,
    total: PropTypes.string.isRequired,
};

export default ProgressBarComponent;
