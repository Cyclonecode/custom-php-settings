<?php

$settings = $this->settings->toOptionsArray();
$pluginSettings = array(
    __('Version', self::TEXT_DOMAIN) => $settings['version'],
    __('Update configuration file', self::TEXT_DOMAIN) => __($settings['update_config'] ? 'yes' : 'no', self::TEXT_DOMAIN),
    __('Restore configuration file', self::TEXT_DOMAIN) => __($settings['restore_config'] ? 'yes' : 'no', self::TEXT_DOMAIN),
    __('Remove comments', self::TEXT_DOMAIN) => __($settings['trim_comments'] ? 'yes' : 'no', self::TEXT_DOMAIN),
    __('Remove whitespaces', self::TEXT_DOMAIN) => __($settings['trim_whitespaces'] ? 'yes' : 'no', self::TEXT_DOMAIN) . PHP_EOL,
    __('=== Custom PHP Settings ===', self::TEXT_DOMAIN) => '',
);
foreach ($settings['php_settings'] as $value) {
    if (!empty($value)) {
        list($key, $val) = explode('=', $value);
        $pluginSettings[$key] = $val;
    }
}
$phpInfo = array(
    __('System name', self::TEXT_DOMAIN) => php_uname(),
    __('Architecture', self::TEXT_DOMAIN) => PHP_INT_SIZE === 8 ? 'x64' : 'x86',
    __('PHP Version', self::TEXT_DOMAIN) => phpversion(),
    __('Debug build', self::TEXT_DOMAIN) => __(defined('ZEND_DEBUG_BUILD') && ZEND_DEBUG_BUILD ? 'yes' : 'no', self::TEXT_DOMAIN),
    __('Zend Engine version', self::TEXT_DOMAIN) => zend_version(),
    __('Server Api', self::TEXT_DOMAIN) => php_sapi_name(),
    __('Configuration File (php.ini) Path', self::TEXT_DOMAIN) => defined('PHP_CONFIG_FILE_PATH') ? PHP_CONFIG_FILE_PATH : '',
    __('Extension directory', self::TEXT_DOMAIN) => defined('PHP_EXTENSION_DIR') ? PHP_EXTENSION_DIR : '',
    __('Loaded configuration file', self::TEXT_DOMAIN) => php_ini_loaded_file(),
    __('Additional configuration files', self::TEXT_DOMAIN) => php_ini_scanned_files(),
    __('Include path', self::TEXT_DOMAIN) => get_include_path(),
    __('User INI file', self::TEXT_DOMAIN) => ini_get('user_ini.filename'),
    __('User INI file cache TTL', self::TEXT_DOMAIN) => ini_get('user_ini.cache_ttl'),
    __('Thread Safety', self::TEXT_DOMAIN) => __(defined('ZEND_THREAD_SAFE') && ZEND_THREAD_SAFE ? 'enabled' : 'disabled', self::TEXT_DOMAIN),
    __('IPv6 Support', self::TEXT_DOMAIN) => __(extension_loaded('sockets') && defined('AF_INET6') ? 'enabled' : 'disabled', self::TEXT_DOMAIN),
    __('PHP Streams', self::TEXT_DOMAIN) => implode(', ', stream_get_wrappers()),
    __('Stream Socket Transports', self::TEXT_DOMAIN) => implode(', ', stream_get_transports()),
    __('Stream Filters', self::TEXT_DOMAIN) => implode(', ', stream_get_filters()),
    __('GC enabled', self::TEXT_DOMAIN) => __(gc_enabled() ? 'enabled' : 'disabled', self::TEXT_DOMAIN) . PHP_EOL,
    __('=== PHP Variables ===', self::TEXT_DOMAIN) => '',
    __('max_execution_time', self::TEXT_DOMAIN) => ini_get('max_execution_time'),
    __('max_input_time', self::TEXT_DOMAIN) => ini_get('max_input_time'),
    __('memory_limit', self::TEXT_DOMAIN) => ini_get('memory_limit'),
    __('upload_max_filesize', self::TEXT_DOMAIN) => ini_get('upload_max_filesize'),
    __('post_max_size', self::TEXT_DOMAIN) => ini_get('post_max_size'),
    __('variables_order', self::TEXT_DOMAIN) => ini_get('variables_order'),
    __('safe_mode', self::TEXT_DOMAIN) => ini_get('safe_mode'),
    __('enable_dl', self::TEXT_DOMAIN) => ini_get('enable_dl'),
    __('register_globals', self::TEXT_DOMAIN) => ini_get('register_globals'),
);

$wpInfo = array(
    __('Version', self::TEXT_DOMAIN) => get_bloginfo('version'),
    __('Multisite', self::TEXT_DOMAIN) => __(is_multisite() ? 'yes' : 'no', self::TEXT_DOMAIN),
    __('Site address', self::TEXT_DOMAIN) => get_bloginfo('url'),
    __('Debug mode', self::TEXT_DOMAIN) => __(WP_DEBUG ? 'yes' : 'no', self::TEXT_DOMAIN),
    __('Memory limit', self::TEXT_DOMAIN) => WP_MEMORY_LIMIT,
    __('Cron', self::TEXT_DOMAIN) => __(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON ? 'no' : 'yes', self::TEXT_DOMAIN),
    __('Language', self::TEXT_DOMAIN) => get_locale(),
);

?>
<div class="wrap">
    <?php require_once('cps-tabs.php'); ?>
    <form id="status-form" action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
        <table class="form-table">
            <tr>
                <td>
                    <textarea name="settings" readonly><?php
                    echo '=== Plugin Settings ===' . PHP_EOL;
                    foreach ($pluginSettings as $key => $value) :
                        echo $key . ($key[0] === '=' ? '' : ' = ' . $value) . PHP_EOL;
                    endforeach;
                    echo PHP_EOL;
                    echo '=== PHP ===' . PHP_EOL;
                    foreach ($phpInfo as $key => $value) :
                        echo $key . ($key[0] === '=' ? '' : ' = ' . $value) . PHP_EOL;
                    endforeach;
                    echo PHP_EOL;
                    echo '=== Wordpress ===' . PHP_EOL;
                    foreach ($wpInfo as $key => $value) :
                        echo $key . ' = ' . $value . PHP_EOL;
                    endforeach;
                    echo PHP_EOL;
                    echo '=== Configuration File ===' . PHP_EOL;
                    $configFilePath = self::getConfigFilePath();
                    if (file_exists($configFilePath)) {
                        echo __('Path', self::TEXT_DOMAIN) . ' = ' . $configFilePath . PHP_EOL;
                        echo __('Readable', self::TEXT_DOMAIN) . ' = ' . __(is_readable($configFilePath) ? 'yes' : 'no', self::TEXT_DOMAIN) . PHP_EOL;
                        echo __('Writeable', self::TEXT_DOMAIN) . ' = ' . __(is_writeable($configFilePath) ? 'yes' : 'no', self::TEXT_DOMAIN) . PHP_EOL;
                    }
                    ?></textarea>
                    <p class="description"><?php _e('If you need help, copy and paste the above information for faster support.', self::TEXT_DOMAIN); ?></p>
                </td>
            </tr>
        </table>
        <button type="button" class="button" onclick="document.querySelector('textarea').select(); document.execCommand('copy');"><?php _e('Copy for support', self::TEXT_DOMAIN); ?></button>
    </form>
</div>
