<?php

namespace CustomPhpSettings\Backend;

use CustomPhpSettings\Common\Singleton;
use CustomPhpSettings\Config\Settings;

class Backend extends Singleton
{
    const VERSION = '1.3.0';
    const SETTINGS_NAME = 'custom_php_settings';
    const TEXT_DOMAIN = 'custom-php-settings';
    const PARENT_MENU_SLUG = 'tools.php';
    const MENU_SLUG = 'custom-php-settings';

    /**
     *
     * @var \CustomPhpSettings\Config\Settings
     */
    private $settings;

    /**
     * Default settings.
     *
     * @var array
     */
    public static $default_settings = array(
        'version' => self::VERSION,
        'php_settings' => array(),
        'restore_config' => true,
    );

    /**
     * @var string $capability
     */
    private $capability = 'manage_options';

    /**
     * @var string $currentTab
     */
    private $currentTab = '';

    /**
     * @var string $currentSection
     */
    private $currentSection = '';

    /**
     * @var string $userIniFileName
     */
    private $userIniFileName = '';

    /**
     * @var int $userIniTTL
     */
    private $userIniTTL = 0;

    /**
     *
     */
    public function init()
    {
        // Allow people to change what capability is required to use this plugin.
        $this->capability = apply_filters('custom_php_settings_cap', $this->capability);

        $this->settings = new Settings(self::SETTINGS_NAME);

        $this->checkForUpgrade();
        $this->setTabs();
        $this->getIniDefaults();
        $this->addActions();
        $this->addFilters();
        $this->localize();
    }

    /**
     * Localize plugin.
     */
    protected function localize()
    {
        load_plugin_textdomain(self::TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Add actions.
     */
    public function addActions()
    {
        // add_action('admin_notices', array($this, 'addQuestion' ), 10);
        add_action('admin_menu', array($this, 'addMenu'));
        add_action('admin_post_custom_php_settings_save_settings', array($this, 'saveSettings'));
        add_action('admin_enqueue_scripts', array($this, 'addScripts'));
    }

    /**
     * Add filters.
     */
    public function addFilters()
    {
        add_filter('admin_footer_text', array($this, 'adminFooter'));
        add_filter('plugin_action_links', array($this, 'addActionLinks'), 10, 2);
    }

    /**
     * Adds admin notification
     */
    public function addQuestion()
    {
        echo '<div class="notice notice-info is-dismissible" style="position: relative;">';
        echo '</div>';
    }

    /**
     * Add action link on plugins page.
     *
     * @param array $links
     * @param string $file
     *
     * @return mixed
     */
    public function addActionLinks($links, $file)
    {
        $settings_link = '<a href="' . admin_url('tools.php?page=custom-php-settings') . '">' .
            __('Settings', self::TEXT_DOMAIN) .
            '</a>';
        if ($file == 'custom-php-settings/bootstrap.php') {
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    /**
     * Add scripts.
     */
    public function addScripts()
    {
        // Added in wordpress 4.1.
        if (function_exists('wp_enqueue_code_editor')) {
            wp_enqueue_code_editor(array());
            wp_enqueue_script(
                'js-code-editor',
                plugin_dir_url(__FILE__) . 'js/code-editor.js',
                array('jquery'),
                '',
                true
            );
        }
        wp_enqueue_style('custom-php-settings', plugin_dir_url(__FILE__) . 'css/admin.css');
    }

    /**
     * Check if any updates needs to be performed.
     */
    public function checkForUpgrade()
    {
        if (version_compare($this->settings->get('version'), self::VERSION, '<')) {
            $this->settings->set('version', self::VERSION);
            $this->settings->save();
        }
    }

    /**
     * Get settings for user INI filename and ttl.
     */
    protected function getIniDefaults()
    {
        $this->userIniFileName = ini_get('user_ini.filename');
        $this->userIniTTL = ini_get('user_ini.cache_ttl');
    }

    /**
     * Set active tab and section.
     */
    protected function setTabs()
    {
        $this->currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
        $this->currentSection = isset($_GET['section']) ? $_GET['section'] : 'php-info';
    }

    /**
     * Returns the active tab.
     *
     * @return string
     */
    protected function getCurrentTab()
    {
        return $this->currentTab;
    }

    /**
     * Returns the active section.
     *
     * @return string
     */
    protected function getCurrentSection()
    {
        return $this->currentSection;
    }

    /**
     * Triggered when plugin is activated.
     */
    public static function activate()
    {
    }

    /**
     * Triggered when plugin is deactivated.
     * Removes any changes in the .htaccess file made by the plugin.
     */
    public static function deActivate()
    {
        $settings = new Settings(self::SETTINGS_NAME);
        if ($settings->get('restore_config')) {
            $config_file = get_home_path() . '.htaccess';
            if (self::getCGIMode()) {
                $userIniFile = ini_get('user_ini.filename');
                $config_file = get_home_path() . $userIniFile;
                self::addMarker($config_file, 'CUSTOM PHP SETTINGS', array(), ';');
            } else {
                self::addMarker($config_file, 'CUSTOM PHP SETTINGS', array());
            }
        }
    }

    /**
     * Uninstalls the plugin.
     */
    public static function delete()
    {
        self::removeSettings();
        delete_option(self::SETTINGS_NAME);
    }

    /**
     * Remove any current settings from either .htaccess or user ini file.
     */
    protected static function removeSettings()
    {
        $configFile = self::getConfigFilePath();
        $settings = new Settings(self::SETTINGS_NAME);
        if ($settings->get('restore_config')) {
            self::addMarker($configFile, 'CUSTOM PHP SETTINGS', array(), self::getCGIMode() ? ';' : '#');
        }
    }

    /**
     * Adds customized text to footer in admin dashboard.
     *
     * @param string $footer_text
     *
     * @return string
     */
    public function adminFooter($footer_text)
    {
        $screen = get_current_screen();
        if ($screen->id == 'tools_page_custom-php-settings') {
            $rate_text = sprintf(
                __('Thank you for using <a href="%1$s" target="_blank">Custom PHP Settings</a>! Please <a href="%2$s" target="_blank">rate us on WordPress.org</a>', self::TEXT_DOMAIN),
                'https://wordpress.org/plugins/custom-php-settings',
                'https://wordpress.org/support/plugin/custom-php-settings/reviews/?rate=5#new-post'
            );

            return '<span>' . $rate_text . '</span>';
        } else {
            return $footer_text;
        }
    }

    /**
     * Add menu item for plugin.
     */
    public function addMenu()
    {
        add_submenu_page(
            self::PARENT_MENU_SLUG,
            __('Custom PHP Settings', self::TEXT_DOMAIN),
            __('Custom PHP Settings', self::TEXT_DOMAIN),
            $this->capability,
            self::MENU_SLUG,
            array($this, 'doSettingsPage')
        );
    }

    /**
     * Add message to be displayed in settings form.
     *
     * @param string $message
     * @param string $type
     */
    protected function addSettingsMessage($message, $type = 'error')
    {
        add_settings_error(
            'custom-php-settings',
            esc_attr('custom-php-settings-updated'),
            $message,
            $type
        );
    }

    /**
     * Check if PHP is running in CGI/Fast-CGI mode or not.
     *
     * @return bool
     */
    protected static function getCGIMode()
    {
        return substr(php_sapi_name(), -3) === 'cgi';
    }

    /**
     * Gets an array of settings to insert into configuration file.
     *
     * @return array
     */
    protected function getSettingsAsArray()
    {
        $cgiMode = $this->getCGIMode();
        $section = array();
        $errors = '';
        foreach ($this->settings->php_settings as $key => $value) {
            if (!empty($value)) {
                $setting = explode('=', trim($value));
                if (count($setting) == 2 && strlen($setting[0]) && strlen($setting[1])) {
                    if ($cgiMode) {
                        $section[] = $setting[0] . '=' . $setting[1];
                    } else {
                        $section[] = 'php_value ' . $setting[0] . ' ' . $setting[1];
                    }
                } else {
                    if (!strlen($setting[0])) {
                        $errors .= __('All settings must be in the format', self::TEXT_DOMAIN) . ' key=value<br/>';
                    } elseif (strpos($setting[0], '#')) {
                        $errors .= sprintf(__('Setting %s is not in a valid format', self::TEXT_DOMAIN), $setting[0]) . '<br />';
                    }
                }
            } else {
                $section[] = '';
            }
        }
        if (!empty($errors)) {
            $this->addSettingsMessage($errors);
        }
        return $section;
    }

    /**
     * Inserts an array of strings into a file (.htaccess ), placing it between
     * BEGIN and END markers.
     *
     * Replaces existing marked info. Retains surrounding
     * data. Creates file if none exists.
     *
     * This is a customized version of insert_with_markers in core.
     *
     * @param string $filename Filename to alter.
     * @param string $marker The marker to alter.
     * @param array|string $insertion The new content to insert.
     * @param string $comment Type of character to use for comments.
     * @return bool True on write success, false on failure.
     */
    protected static function addMarker($filename, $marker, $insertion, $comment = '#')
    {
//        if (self::createIfNotExist($filename) === false) {
//            return false;
//        }

        if (!is_array($insertion)) {
            $insertion = explode("\n", $insertion);
        }

        $start_marker = "$comment BEGIN {$marker}";
        $end_marker = "$comment END {$marker}";

        $fp = fopen($filename, 'r+');
        if (!$fp) {
            return false;
        }

        // Attempt to get a lock. If the filesystem supports locking, this will block until the lock is acquired.
        flock($fp, LOCK_EX);

        $lines = array();
        while (!feof($fp)) {
            $lines[] = rtrim(fgets($fp), "\r\n");
        }

        // Split out the existing file into the preceding lines, and those that appear after the marker
        $pre_lines = $post_lines = $existing_lines = array();
        $found_marker = $found_end_marker = false;
        foreach ($lines as $line) {
            if (!$found_marker && false !== strpos($line, $start_marker)) {
                $found_marker = true;
                continue;
            } elseif (!$found_end_marker && false !== strpos($line, $end_marker)) {
                $found_end_marker = true;
                continue;
            }
            if (!$found_marker) {
                $pre_lines[] = $line;
            } elseif ($found_marker && $found_end_marker) {
                $post_lines[] = $line;
            } else {
                $existing_lines[] = $line;
            }
        }

        // Check to see if there was a change
        if ($existing_lines === $insertion) {
            flock($fp, LOCK_UN);
            fclose($fp);
            return true;
        }

        // Generate the new file data
        $new_file_data = implode(
            "\n",
            array_merge(
                $pre_lines,
                array($start_marker),
                $insertion,
                array($end_marker),
                $post_lines
            )
        );

        // Write to the start of the file, and truncate it to that length
        fseek($fp, 0);
        $bytes = fwrite($fp, $new_file_data);
        if ($bytes) {
            ftruncate($fp, ftell($fp));
        }
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        return (bool) $bytes;
    }

    /**
     * Try to store settings in either .htaccess or .ini file.
     */
    protected function updateConfigFile()
    {
        $configFile = self::getConfigFilePath();
//        if ((!file_exists($configFile) && is_writeable($home_path)) || is_writable($configFile)) {
//
//        }
        if (self::createIfNotExist($configFile) === false) {
            $this->addSettingsMessage(sprintf(__('%s does not exists or is not writable.', self::TEXT_DOMAIN), $configFile));
            return;
        }
        $section = $this->getSettingsAsArray();
        $message = sprintf(__('Settings updated and stored in %s.', self::TEXT_DOMAIN), $configFile);
        $this->addSettingsMessage($message, 'updated');
        self::addMarker($configFile, 'CUSTOM PHP SETTINGS', $section, self::getCGIMode() ? ';' : '#');
    }

    /**
     * Check so file exists and is writable.
     *
     * @param string $filename
     *
     * @return bool
     */
    protected static function createIfNotExist($filename)
    {
        $fp = null;
        if (!file_exists($filename)) {
            if (!is_writable(dirname($filename))) {
                return false;
            }
            if (!touch($filename)) {
                return false;
            }
            // Make sure the file is created with a minimum set of permissions.
            $perms = fileperms($filename);
            if ($perms) {
                chmod($filename, $perms | 0644);
            }
        } elseif (!($fp = @fopen($filename, 'a'))) {
            return false;
        }
        if ($fp) {
            fclose($fp);
        }
    }

    /**
     * Validates so a line is either a comment, blank line or a valid setting.
     *
     * @param string $setting
     *
     * @return int
     */
    protected function validSetting($setting)
    {
        $iniSettings = array_keys($this->getIniSettings());
        $setting = explode('=', $setting);
        if (count($setting) == 1 && strpos($setting[0], '#') !== false) {
            // This is a comment.
            return 1;
        }
        if (count($setting) == 1 && strlen($setting[0]) === 0) {
            // This is a blank line.
            return 1;
        }
        if (count($setting) == 2 && in_array($setting[0], $iniSettings)) {
            return 1;
        }
        return 0;
    }

    /**
     * Handle form data for configuration page.
     * @todo Better handling of settings during save.
     *
     * @return bool
     */
    public function saveSettings()
    {
        // Check if settings form is submitted.
        if (filter_input(INPUT_POST, 'custom-php-settings', FILTER_SANITIZE_STRING)) {
            // Validate so user has correct privileges.
            if (!current_user_can($this->capability)) {
                die(__('You are not allowed to perform this action.', self::TEXT_DOMAIN));
            }

            // Verify nonce and referer.
            if (check_admin_referer('custom-php-settings-action', 'custom-php-settings-nonce')) {
                $errors = '';
                // Filter and sanitize form values.
                $raw_settings = filter_input(
                    INPUT_POST,
                    'php-settings',
                    FILTER_SANITIZE_STRING
                );
                $raw_settings = rtrim($raw_settings);
                $raw_settings = explode(PHP_EOL, $raw_settings);
                $raw_settings = array_map('trim', $raw_settings);
                $settings = array();
                foreach ($raw_settings as $key => $value) {
                    if ($this->validSetting($value)) {
                        $settings[$key] = str_replace(';', '', $value);
                    } else {
                        $setting = explode('=', $value);
                        $errors .= sprintf(__('%s is not a valid setting.', self::TEXT_DOMAIN), $setting[0]) . '<br />';
                    }
                }
                if (!empty($errors)) {
                    $this->addSettingsMessage($errors);
                }
                $this->settings->set('php_settings', $settings);

                $this->updateConfigFile();

                $this->settings->restore_config = filter_input(
                    INPUT_POST,
                    'restore-config',
                    FILTER_VALIDATE_BOOLEAN
                );

                $this->settings->save();

                set_transient('cps_settings_errors', get_settings_errors());
                wp_safe_redirect(admin_url(self::PARENT_MENU_SLUG . '?page=' . self::MENU_SLUG));
            }
        }
    }

    /**
     * Returns absolute path to configuration file.
     */
    protected static function getConfigFilePath()
    {
        return get_home_path() . (self::getCGIMode() ? ini_get('user_ini.filename') : '.htaccess');
    }

    /**
     * Get all non-system settings.
     *
     * @return array
     */
    protected function getIniSettings()
    {
        return array_filter(ini_get_all(), function ($item) {
            return ($item['access'] !== INI_SYSTEM);
        });
    }

    /**
     * Display the settings page.
     */
    public function doSettingsPage()
    {
        // Display any settings messages
        $setting_errors = get_transient('cps_settings_errors');
        if ($setting_errors) {
            foreach ($setting_errors as $error) {
                $this->addSettingsMessage($error['message'], $error['type']);
            }
            delete_transient('cps_settings_errors');
        }
        if ($this->getCurrentTab() === 'settings') {
            require_once __DIR__ . '/views/cps-settings-table.php';
        }
        if ($this->getCurrentTab() === 'info' && $this->getCurrentSection()) {
            $template = __DIR__ . '/views/cps-' . $this->currentSection . '.php';
            if (file_exists($template)) {
                require_once $template;
            }
        }
        if ($this->getCurrentTab() === 'general') {
            require_once __DIR__ . '/views/cps-general.php';
        }
        if ($this->getCurrentTab() === 'apache') {
            require_once __DIR__ . '/views/cps-apache.php';
        }
    }

    /**
     * Format bytes
     *
     * @param $bytes
     * @param int $precision
     * @return string
     */
    public function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
