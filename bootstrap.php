<?php

/**
 * Plugin Name: Custom PHP settings
 * Plugin URI: https://wordpress.org/plugins/custom-php-settings/
 * Description: Customize PHP settings.
 * Version: 1.4.0
 * Requires at least: 3.1.0
 * Requires PHP: 5.4
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

require_once __DIR__ . '/vendor/autoload.php';

use CustomPhpSettings\Backend\Backend;

add_action('plugins_loaded', function () {
    if (is_admin()) {
        Backend::getInstance();
    }
});

register_activation_hook(__FILE__, array('CustomPhpSettings\Backend\Backend', 'activate'));
register_deactivation_hook(__FILE__, array('CustomPhpSettings\Backend\Backend', 'deActivate'));
register_uninstall_hook(__FILE__, array('CustomPhpSettings\Backend\Backend', 'delete'));
