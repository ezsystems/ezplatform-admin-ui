import EzConfigTableBase from './base-table';

export default class EzTableRowConfig extends EzConfigTableBase {
    getConfigName() {
        return 'tr';
    }

    test(payload) {
        const nativeEditor = payload.editor.get('nativeEditor');
        const path = nativeEditor.elementPath();

        return path && path.lastElement.is('tr');
    }
}

eZ.addConfig('ezAlloyEditor.ezTableRowConfig', EzTableRowConfig);
