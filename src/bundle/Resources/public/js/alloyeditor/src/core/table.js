(function(global, doc, CKEDITOR) {
    if (!CKEDITOR.Table) {
        return;
    }

    const originalCreate = CKEDITOR.Table.prototype.create;

    CKEDITOR.Table.prototype.create = function(config) {
        if (this._editor.widgets && this._editor.widgets.selected.length) {
            this._editor.execCommand('eZAddContent', {
                tagName: 'p',
                content: '<br>',
            });

            this._editor.selectionChange(true);
        }

        return originalCreate.call(this, config);
    };
})(window, window.document, window.CKEDITOR);
