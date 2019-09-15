'use strict';
(function ($) {
    $(function () {
        if ($('#code_editor_custom_php_settings').length) {
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
            let editor = wp.codeEditor.initialize($('#code_editor_custom_php_settings'), editorSettings);
        }
    });
})(jQuery);
