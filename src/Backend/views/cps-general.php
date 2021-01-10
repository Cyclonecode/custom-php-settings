<?php
$settings = '';
$php_settings = $this->settings->get('php_settings');
if (isset($php_settings)) {
    foreach ($php_settings as $key => $value) {
        $settings .= $value . PHP_EOL;
    }
}
?>
<div class="wrap">
    <?php do_action('custom_php_settings_admin_notices'); ?>
    <?php settings_errors(); ?>
    <h1></h1>
    <?php require_once('cps-tabs.php'); ?>
    <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
    <?php wp_nonce_field('custom-php-settings-action', 'custom-php-settings-nonce'); ?>
        <input type="hidden" name="action" value="custom_php_settings_save_settings" />
        <table class="form-table">
            <tr>
                <td>
                    <fieldset>
                        <textarea id="code_editor_custom_php_settings"
                                  rows="5"
                                  name="php_settings"
                                  class="widefat textarea"><?php echo $settings; ?></textarea>
                    </fieldset>
                    <p class="description"><?php echo __('Custom PHP Settings. Each setting should be in the form key=value.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" name="update_config"<?php checked($this->settings->get('update_config')); ?> />
                    <span class="description"><?php echo __('Update configuration file.', self::TEXT_DOMAIN); ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" name="restore_config"<?php checked($this->settings->get('restore_config')); ?> />
                    <span class="description"><?php echo __('The configuration file will be restored when the plugin is deactivated or uninstalled.', self::TEXT_DOMAIN); ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" name="trim_comments"<?php checked($this->settings->get('trim_comments')); ?> />
                    <span class="description"><?php echo __('Do not store any comments in the configuration file.', self::TEXT_DOMAIN); ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" name="trim_whitespaces"<?php checked($this->settings->get('trim_whitespaces')); ?> />
                    <span class="description"><?php echo __('Do not store any blank lines in the configuration file.', self::TEXT_DOMAIN); ?></span>
                </td>
            </tr>
        </table>
        <?php echo get_submit_button(__('Save settings', self::TEXT_DOMAIN), 'primary', 'custom-php-settings'); ?>
    </form>
</div>
