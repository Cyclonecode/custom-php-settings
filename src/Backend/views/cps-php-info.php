<?php
$env = array(
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
    __('PHP Script Owner', self::TEXT_DOMAIN) => get_current_user(),
    __('PHP Script Owner UID', self::TEXT_DOMAIN) => getmyuid(),
    __('PHP Script Owner GUID', self::TEXT_DOMAIN) => getmygid(),
    __('PHP process ID', self::TEXT_DOMAIN) => getmypid(),
    __('Memory usage', self::TEXT_DOMAIN) => $this->formatBytes(memory_get_usage()),
    __('Memory peak usage', self::TEXT_DOMAIN) => $this->formatBytes(memory_get_peak_usage()),
    __('Temporary directory', self::TEXT_DOMAIN) => sys_get_temp_dir(),
    __('User INI file', self::TEXT_DOMAIN) => ini_get('user_ini.filename'),
    __('User INI file cache TTL', self::TEXT_DOMAIN) => ini_get('user_ini.cache_ttl'),
    __('Thread Safety', self::TEXT_DOMAIN) => __(defined('ZEND_THREAD_SAFE') && ZEND_THREAD_SAFE ? 'enabled' : 'disabled', self::TEXT_DOMAIN),
    __('IPv6 Support', self::TEXT_DOMAIN) => __(extension_loaded('sockets') && defined('AF_INET6') ? 'enabled' : 'disabled', self::TEXT_DOMAIN),
    __('PHP Streams', self::TEXT_DOMAIN) => implode(', ', stream_get_wrappers()),
    __('Stream Socket Transports', self::TEXT_DOMAIN) => implode(', ', stream_get_transports()),
    __('Stream Filters', self::TEXT_DOMAIN) => implode(', ', stream_get_filters()),
    __('GC enabled', self::TEXT_DOMAIN) => __(gc_enabled() ? 'enabled' : 'disabled', self::TEXT_DOMAIN),
);
?>
<div class="wrap">
    <?php require_once('cps-tabs.php'); ?>
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
