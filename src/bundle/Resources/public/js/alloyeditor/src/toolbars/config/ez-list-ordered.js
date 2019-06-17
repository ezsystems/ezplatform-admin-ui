import EzConfigListBase from './base-list';

export default class EzListOrderedConfig extends EzConfigListBase {
    getConfigName() {
        return 'ol';
    }

    test(payload) {
        const nativeEditor = payload.editor.get('nativeEditor');
        const path = nativeEditor.elementPath();

        return path && path.lastElement.is('ol');
    }
}

eZ.addConfig('ezAlloyEditor.ezListOrderedConfig', EzListOrderedConfig);
