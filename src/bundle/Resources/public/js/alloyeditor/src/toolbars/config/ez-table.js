import EzConfigTableBase from './base-table';

export default class EzTableConfig extends EzConfigTableBase {
    getConfigName() {
        return 'table';
    }

    test(payload) {
        const nativeEditor = payload.editor.get('nativeEditor');
        const path = nativeEditor.elementPath();

        return path && path.lastElement.is('table');
    }
}

eZ.addConfig('ezAlloyEditor.ezTableConfig', EzTableConfig);
