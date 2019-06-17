(function(global, doc, eZ, AlloyEditor) {
    const { attributes, classes } = eZ.adminUiConfig.alloyEditor;
    const toolbarNames = new Set([...Object.keys(attributes), ...Object.keys(classes)]);

    toolbarNames.forEach((toolbarName) => {
        const componentClassName = `ezBtn${toolbarName.charAt(0).toUpperCase() + toolbarName.slice(1)}`;
        const editComponentClassName = `${componentClassName}Edit`;
        const updateComponentClassName = `${componentClassName}Update`;
        const toolbarClasses = classes[toolbarName] || {};
        const toolbarAttributes = attributes[toolbarName];

        class ButtonAttributesEdit extends eZ.ezAlloyEditor.ezBtnAttributesEdit {
            constructor(props) {
                super(props);

                this.toolbarName = toolbarName;
                this.classes = toolbarClasses;
                this.attributes = toolbarAttributes || {};
            }

            static get key() {
                return `${toolbarName}edit`;
            }
        }

        class ButtonAttributesUpdate extends eZ.ezAlloyEditor.ezBtnAttributesUpdate {
            constructor(props) {
                super(props);

                this.toolbarName = toolbarName;
                this.classes = toolbarClasses;
                this.attributes = toolbarAttributes || {};
            }

            static get key() {
                return `${toolbarName}update`;
            }
        }

        AlloyEditor.Buttons[ButtonAttributesEdit.key] = AlloyEditor[editComponentClassName] = ButtonAttributesEdit;
        AlloyEditor.Buttons[ButtonAttributesUpdate.key] = AlloyEditor[updateComponentClassName] = ButtonAttributesUpdate;
    });
})(window, window.document, window.eZ, window.AlloyEditor);
