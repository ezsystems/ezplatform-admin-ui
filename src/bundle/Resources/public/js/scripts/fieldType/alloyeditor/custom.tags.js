(function(global, doc) {
    Object.keys(global.eZ.adminUiConfig.richTextCustomTags).forEach((customTag) => {
        const tagConfig = global.eZ.adminUiConfig.richTextCustomTags[customTag];
        const componentClassName = `ezBtn${customTag.charAt(0).toUpperCase() + customTag.slice(1)}`;
        const editComponentClassName = `${componentClassName}Edit`;
        const updateComponentClassName = `${componentClassName}Update`;

        class ButtonCustomTag extends global.eZ.ezAlloyEditor.ezBtnCustomTag {
            constructor(props) {
                super(props);

                const values = {};

                Object.entries(tagConfig.attributes).forEach(([attr, value]) => {
                    values[attr] = {
                        value: value.defaultValue,
                    };
                });

                this.label = tagConfig.label;
                this.icon = tagConfig.icon || '/bundles/ezplatformadminui/img/ez-icons.svg#tag';
                this.customTagName = customTag;
                this.values = values;
            }

            static get key() {
                return customTag;
            }
        }

        class ButtonCustomTagEdit extends global.eZ.ezAlloyEditor.ezBtnCustomTagEdit {
            constructor(props) {
                super(props);

                this.customTagName = customTag;
                this.attributes = tagConfig.attributes;
            }

            static get key() {
                return `${customTag}edit`;
            }
        }

        class ButtonCustomTagUpdate extends global.eZ.ezAlloyEditor.ezBtnCustomTagUpdate {
            constructor(props) {
                super(props);

                this.customTagName = customTag;
                this.attributes = tagConfig.attributes;
            }

            static get key() {
                return `${customTag}update`;
            }
        }

        global.AlloyEditor.Buttons[ButtonCustomTag.key] = global.AlloyEditor[componentClassName] = ButtonCustomTag;
        global.AlloyEditor.Buttons[ButtonCustomTagEdit.key] = global.AlloyEditor[editComponentClassName] = ButtonCustomTagEdit;
        global.AlloyEditor.Buttons[ButtonCustomTagUpdate.key] = global.AlloyEditor[updateComponentClassName] = ButtonCustomTagUpdate;
    });
})(window, document);
