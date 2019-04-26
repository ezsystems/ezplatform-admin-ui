import EzConfigListBase from './base-list';

export default class EzListUnorderedConfig extends EzConfigListBase {
    getConfigName() {
        return 'ul';
    }

    test(payload) {
        const nativeEditor = payload.editor.get('nativeEditor');
        const path = nativeEditor.elementPath();

        return path && path.lastElement.is('ul');
    }
}

eZ.addConfig('ezAlloyEditor.ezListUnorderedConfig', EzListUnorderedConfig);
