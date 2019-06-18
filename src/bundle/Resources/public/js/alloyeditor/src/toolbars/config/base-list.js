import EzConfigBase from './base';

export default class EzListBaseConfig extends EzConfigBase {
    constructor(config) {
        super(config);

        this.name = this.getConfigName();
        this.buttons = ['ezmoveup', 'ezmovedown', this.getEditAttributesButton(config), 'ezembedinline', 'ezanchor', 'ezblockremove'];

        this.addExtraButtons(config.extraButtons);
    }

    getConfigName() {
        return '';
    }
}
