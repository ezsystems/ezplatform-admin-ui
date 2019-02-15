import PropTypes from 'prop-types';
import EzBtnCustomTagEdit from './ez-btn-customtag-edit';

export default class EzBtnInlineCustomTagEdit extends EzBtnCustomTagEdit {
    getUpdateBtnName() {
        return `ezBtn${this.customTagName.charAt(0).toUpperCase() + this.customTagName.slice(1)}Update`;
    }
}

eZ.addConfig('ezAlloyEditor.ezBtnInlineCustomTagEdit', EzBtnInlineCustomTagEdit);

EzBtnInlineCustomTagEdit.propTypes = {
    editor: PropTypes.object.isRequired,
    label: PropTypes.string.isRequired,
    tabIndex: PropTypes.number.isRequired,
};
