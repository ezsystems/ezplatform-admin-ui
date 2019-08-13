import EzConfigTableBase from './base-table';

export default class EzTableCellConfig extends EzConfigTableBase {
    getConfigName() {
        return 'td';
    }

    test(payload) {
        const nativeEditor = payload.editor.get('nativeEditor');
        const path = nativeEditor.elementPath();

        return path && path.lastElement.is('td');
    }
}

eZ.addConfig('ezAlloyEditor.ezTableCellConfig', EzTableCellConfig);
