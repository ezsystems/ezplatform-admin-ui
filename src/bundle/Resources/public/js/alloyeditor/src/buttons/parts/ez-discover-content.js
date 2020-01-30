import React, { createRef } from 'react';
import PropTypes from 'prop-types';
import AlloyEditor from 'alloyeditor';
import EzEmbedDiscoverContentButton from '../../base/ez-embeddiscovercontent';

export default class EzDiscoverContent extends EzEmbedDiscoverContentButton {
    constructor(props) {
        super(props);

        this.originalSetUIHidden = null;
        this.buttonRef = createRef(null);
    }

    confirmSelection() {
        const { confirmSelectedItems, editor } = this.props;

        confirmSelectedItems(...arguments);

        ReactDOM.unmountComponentAtNode(document.querySelector('#react-udw'));

        editor._mainUI._setUIHidden = this.originalSetUIHidden;

        this.buttonRef.current.focus();
    }

    cancelHandler(udwContainer) {
        super.cancelHandler(udwContainer);

        this.props.editor._mainUI._setUIHidden = this.originalSetUIHidden;

        this.buttonRef.current.focus();
    }

    chooseContent() {
        const { editor } = this.props;

        this.originalSetUIHidden = editor._mainUI._setUIHidden;

        editor._mainUI._setUIHidden = () => {};

        super.chooseContent();
    }

    render() {
        const buttonLabel = Translator.trans(/*@Desc("Select Content")*/ 'discover_content.button.label', {}, 'alloy_editor');

        return (
            <div>
                <button className="btn btn-secondary ez-btn-ae" ref={this.buttonRef} onClick={this.chooseContent.bind(this)}>
                    {buttonLabel}
                </button>
            </div>
        );
    }
}

EzDiscoverContent.propTypes = {
    editor: PropTypes.object.isRequired,
    udwConfigName: PropTypes.string.isRequired,
    confirmSelectedItems: PropTypes.func.isRequired,
    udwContentDiscoveredMethod: PropTypes.string,
};

EzDiscoverContent.defaultProps = {
    udwContentDiscoveredMethod: 'confirmSelection',
};
