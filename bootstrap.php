<?php
/**
 * Plugin Name: Custom PHP settings
 * Description: Customize PHP settings.
 * Version: 1.2.6
 * Author: Cyclonecode
 * Author URI: https://stackoverflow.com/users/1047662/cyclonecode?tab=profile
 * Copyright: Cyclonecode
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: custom-php-settings
 * Domain Path: /languages
 *
 * @package custom-php-settings
 * @author custom-php-settings
 */
namespace CustomPhpSettings;

/**
 * Exit if accessed directly.
 */
if (!defined('ABSPATH')) {
    exit;
}

define('CUSTOM_PHP_SETTINGS_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once CUSTOM_PHP_SETTINGS_PLUGIN_DIR . '/admin/custom-php-settings.php';

add_action('plugins_loaded', function () {
    if (is_admin()) {
        CustomPhpSettings::getInstance()->initialize();
    }
});

register_activation_hook(__FILE__, array('CustomPhpSettings\CustomPhpSettings', 'activate'));
register_deactivation_hook(__FILE__, array('CustomPhpSettings\CustomPhpSettings', 'deActivate'));
register_uninstall_hook(__FILE__, array('CustomPhpSettings\CustomPhpSettings', 'delete'));
