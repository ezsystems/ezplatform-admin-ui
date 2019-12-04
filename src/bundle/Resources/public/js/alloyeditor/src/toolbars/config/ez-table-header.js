import EzConfigTableBase from './base-table';

export default class EzTableHeaderConfig extends EzConfigTableBase {
    getConfigName() {
        return 'th';
    }

    test(payload) {
        const nativeEditor = payload.editor.get('nativeEditor');
        const path = nativeEditor.elementPath();

        return path && path.lastElement.is('th');
    }
}

eZ.addConfig('ezAlloyEditor.ezTableHeaderConfig', EzTableHeaderConfig);
