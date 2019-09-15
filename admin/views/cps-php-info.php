<?php
$env = array(
    __('System name', self::TEXT_DOMAIN) => php_uname(),
    __('PHP Version', self::TEXT_DOMAIN) => phpversion(),
    __('Zend Engine version', self::TEXT_DOMAIN) => zend_version(),
    __('Server Api', self::TEXT_DOMAIN) => php_sapi_name(),
    __('Loaded configuration file', self::TEXT_DOMAIN) => php_ini_loaded_file(),
    __('PHP Script Owner', self::TEXT_DOMAIN) => get_current_user(),
    __('PHP Script Owner UID', self::TEXT_DOMAIN) => getmyuid(),
    __('PHP Script Owner GUID', self::TEXT_DOMAIN) => getmygid(),
    __('Memory usage', self::TEXT_DOMAIN) => memory_get_usage(),
    __('Memory peak usage', self::TEXT_DOMAIN) => memory_get_peak_usage(),
    __('Temporary directory', self::TEXT_DOMAIN) => sys_get_temp_dir(),
    __('User INI file', self::TEXT_DOMAIN) => $this->userIniFileName,
    __('User INI file cache TTL', self::TEXT_DOMAIN) => $this->userIniTTL,
);
?>
<div class="wrap">
    <h1><?php echo __('PHP Information', self::TEXT_DOMAIN); ?></h1>
    <?php require_once(CUSTOM_PHP_SETTINGS_PLUGIN_DIR . 'admin/views/cps-tabs.php'); ?>
    <?php settings_errors(); ?>
    <table class="custom-php-settings-table widefat">
        <thead>
            <th><?php echo __('Name', self::TEXT_DOMAIN); ?></th>
            <th><?php echo __('Value', self::TEXT_DOMAIN); ?></th>
        </thead>
        <?php foreach ($env as $key => $value) : ?>
        <tr>
            <td><?php echo $key; ?></td>
            <td><?php echo $value; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
