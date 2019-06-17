import EzConfigTableBase from './base-table';

export default class EzTableCellConfig extends EzConfigTableBase {
    getConfigName() {
        return 'td';
    }

    test(payload) {
        const nativeEditor = payload.editor.get('nativeEditor');
        const path = nativeEditor.elementPath();
        const lastElement = path.lastElement;

        return lastElement.is('td');
    }
}

eZ.addConfig('ezAlloyEditor.ezTableCellConfig', EzTableCellConfig);
