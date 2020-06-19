<?php
$settings = '';
$php_settings = $this->settings->get('php_settings');
if (isset($php_settings) && count($php_settings)) {
    foreach ($php_settings as $setting) {
        $settings .= $setting . PHP_EOL;
    }
}
?>
<div class="wrap">
    <h1><?php echo __('Custom PHP Settings', self::TEXT_DOMAIN); ?></h1>
    <?php require_once(CUSTOM_PHP_SETTINGS_PLUGIN_DIR . 'admin/views/cps-tabs.php'); ?>
    <?php settings_errors(); ?>
    <form action="" method="POST">
    <?php wp_nonce_field('custom-php-settings-action', 'custom-php-settings-nonce'); ?>
        <table class="form-table">
            <tr>
                <td>
                    <fieldset>
                        <textarea id="code_editor_custom_php_settings"
                                  rows="5"
                                  name="php-settings"
                                  class="widefat textarea"><?php echo $settings; ?></textarea>
                    </fieldset>
                    <p class="description"><?php echo __('Custom PHP Settings. Each setting should be in the form key=value.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" name="restore-config"<?php echo ($this->settings->get('restore_config') ? ' checked="checked"' : ''); ?> />
                    <p class="description"><?php echo __('If this option is selected the .htaccess or user INI file will be restored when the plugin is deactivated or uninstalled.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
        </table>
        <?php echo get_submit_button(__('Save settings', self::TEXT_DOMAIN), 'primary', 'custom-php-settings'); ?>
    </form>
</div>
