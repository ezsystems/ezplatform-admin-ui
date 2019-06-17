import EzConfigListBase from './base-list';

export default class EzListItemConfig extends EzConfigListBase {
    getConfigName() {
        return 'li';
    }

    test(payload) {
        const nativeEditor = payload.editor.get('nativeEditor');
        const path = nativeEditor.elementPath();

        return path && path.lastElement.is('li');
    }
}

eZ.addConfig('ezAlloyEditor.ezListItemConfig', EzListItemConfig);
