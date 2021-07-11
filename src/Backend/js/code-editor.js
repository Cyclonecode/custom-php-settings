'use strict';
(($) => {
    $(() => {
        const editor = $('#code_editor_custom_php_settings')
        if (editor.length) {
            let editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror,
                {
                    indentUnit: 2,
                    tabSize: 2,
                    mode: 'shell',
                }
            );
            wp.codeEditor.initialize(editor, editorSettings);
        }
    });
})(jQuery);
