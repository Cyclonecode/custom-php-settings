<?php

namespace CustomPhpSettings\Backend;

use Cyclonecode\Plugin\Singleton;
use Cyclonecode\Plugin\Settings;

class Backend extends Singleton
{
    const VERSION = '1.4.0';
    const SETTINGS_NAME = 'custom_php_settings';
    const TEXT_DOMAIN = 'custom-php-settings';
    const PARENT_MENU_SLUG = 'tools.php';
    const MENU_SLUG = 'custom-php-settings';

    /**
     *
     * @var Settings
     */
    private $settings;

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
     *
     */
    public function init()
    {
        // Allow people to change what capability is required to use this plugin.
        $this->capability = apply_filters('custom_php_settings_cap', $this->capability);

        $this->settings = new Settings(self::SETTINGS_NAME);

        $this->checkForUpgrade();
        $this->setTabs();
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
        add_action('admin_menu', array($this, 'addMenu'));
        add_action('in_admin_header', array($this, 'addHeader'));
        add_action('admin_post_custom_php_settings_save_settings', array($this, 'saveSettings'));
        add_action('admin_enqueue_scripts', array($this, 'addScripts'));
        add_action('custom_php_settings_admin_notices', array($this, 'renderNotices'));
        add_action('wp_ajax_custom_php_settings_dismiss_notice', array($this, 'doDismissNotice'));
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
     * Marks a notification as dismissed.
     *
     * @param string $id
     * @return bool
     */
    private function dismissNotice($id)
    {
        $notes = $this->settings->get('notes');
        foreach ($notes as $key => $note) {
            if ($note['id'] === (int) $id) {
                $notes[$key]['dismissed'] = true;
                $notes[$key]['time'] = time();
                $this->settings->set('notes', $notes);
                $this->settings->save();
                return true;
            }
        }
    }

    /**
     * Resets a notification.
     *
     * @param string $id
     * @return bool
     */
    public function resetNotice($id)
    {
        $notes = $this->settings->get('notes');
        foreach ($notes as $key => $note) {
            if ($note['id'] === (int) $id) {
                $notes[$key]['dismissed'] = false;
                $notes[$key]['time'] = time();
                $this->settings->set('notes', $notes);
                $this->settings->save();
                return true;
            }
        }
    }

    /**
     * Returns a notification by name.
     *
     * @param string $name
     * @return mixed|null
     */
    public function getNoticeByName($name)
    {
        $notes = $this->settings->get('notes');
        return isset($notes[$name]) ? $notes[$name] : null;
    }

    /**
     * Render any notifications.
     */
    public function renderNotices()
    {
        foreach ($this->settings->get('notes') as $note) {
            if (!$note['dismissed'] || ($note['dismissed'] && !$note['persistent'] && time() - $note['time'] > 30 * 24 * 60 * 60)) {
                echo call_user_func(array($this, $note['callback']));
            }
        }
    }

    /**
     * Ajax handler for dismissing notifications.
     */
    public function doDismissNotice()
    {
        check_ajax_referer('custom_php_settings_dismiss_notice');
        if (!current_user_can('administrator')) {
            return wp_send_json_error(__('You are not allowed to perform this action.', self::TEXT_DOMAIN));
        }
        if (!filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)) {
            return wp_send_json_error(__('No valid notification id supplied.', self::TEXT_DOMAIN));
        }
        if (!$this->dismissNotice($_POST['id'])) {
            return wp_send_json_error(__('Notification could not be found.', self::TEXT_DOMAIN));
        }
        wp_send_json_success();
    }

    /**
     * Adds review admin notification.
     */
    public function addReviewNotice()
    {
        ?>
        <div id="note-1" class="custom-php-settings-notice notice-info notice is-dismissible inline" style="position: relative;">
            <h3><?php _e('Thank you for using Custom PHP Settings!', self::TEXT_DOMAIN); ?></h3>
            <p><?php echo sprintf(__('If you use and enjoy Custom PHP Settings, I would be really grateful if you could give it a positive review at <a href="%s" target="_blank">Wordpress.org</a>.', self::TEXT_DOMAIN), 'https://wordpress.org/support/plugin/custom-php-settings/reviews/?rate=5#new-post'); ?></p>
            <p><?php _e('Doing this would help me keeping the plugin free and up to date.', self::TEXT_DOMAIN); ?></p>
            <p><?php _e('Thank you very much!', self::TEXT_DOMAIN); ?></p>
        </div>
        <?php
    }

    /**
     * Adds support admin notification.
     */
    public function addSupportNotice()
    {
        ?>
        <div id="note-2" class="custom-php-settings-notice notice notice-info is-dismissible" style="position: relative;">
            <h3><?php _e('Do you have any feedback or need support?', self::TEXT_DOMAIN); ?></h3>
            <p><?php echo sprintf(__('If you have any request for improvement or just need some help. Do not hesitate to open a ticket in the <a href="%s" target="_blank">support section</a>.', self::TEXT_DOMAIN), 'https://wordpress.org/support/plugin/custom-php-settings/#new-topic-0'); ?></p>
            <p><?php echo sprintf(__('I can also be reached by email at <a href="%s">%s</a>', self::TEXT_DOMAIN), 'mailto:cyclonecode.help@gmail.com?subject=Custom PHP Settings Support', 'cyclonecode.help@gmail.com'); ?></p>
            <p><?php _e('I hope you will have an awesome day!', self::TEXT_DOMAIN); ?></p>
        </div>
        <?php
    }

    /**
     * Render admin header.
     */
    public function addHeader()
    {
        if (get_current_screen()->id !== 'tools_page_custom-php-settings') {
            return;
        }
        $sectionText = array(
                'general' =>  __('Editor', self::TEXT_DOMAIN),
                'apache' => __('Apache Information', self::TEXT_DOMAIN),
                'php-info' => __('PHP Information', self::TEXT_DOMAIN),
                'extensions' => __('Loaded Extensions', self::TEXT_DOMAIN),
                'settings' => __('Current PHP Settings', self::TEXT_DOMAIN),
                'cookie-vars' => __('COOKIE Variables', self::TEXT_DOMAIN),
                'server-vars' => __('SERVER Variables', self::TEXT_DOMAIN),
                'env-vars' => __('ENV Variables', self::TEXT_DOMAIN),
        );
        if ($this->currentTab === 'info' && !empty($this->currentSection)) {
            $title = ' | ' . $sectionText[$this->currentSection];
        } else {
            $title = $this->currentTab ? ' | ' . $sectionText[$this->currentTab] : '';
        }
        ?>
        <div id="custom-php-settings-admin-header">
            <span><img width="64" src="<?php echo plugin_dir_url(__FILE__); ?>assets/icon-128x128.png" alt="<?php _e('Custom PHP Settings', self::TEXT_DOMAIN); ?>" />
                <h1><?php _e('Custom PHP Settings', self::TEXT_DOMAIN); ?><?php echo $title; ?></h1>
            </span>
        </div>
        <?php
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
        if ($file === 'custom-php-settings/bootstrap.php') {
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    /**
     * Add scripts.
     */
    public function addScripts($hook)
    {
        if ($hook !== 'tools_page_custom-php-settings') {
            return;
        }
        // Added in wordpress 4.1.
        if (function_exists('wp_enqueue_code_editor') && $this->getCurrentTab() === 'general') {
            wp_enqueue_code_editor(array());
            wp_enqueue_script(
                'js-code-editor',
                plugin_dir_url(__FILE__) . 'js/code-editor.js',
                array('jquery'),
                '',
                true
            );
        }
        wp_enqueue_script(
            'custom-php-settings',
            plugin_dir_url(__FILE__) . 'js/admin.js',
            array('jquery'),
            '',
            true
        );
        wp_localize_script('custom-php-settings', 'data', array(
            '_nonce' => wp_create_nonce('custom_php_settings_dismiss_notice'),
        ));
        wp_enqueue_style('custom-php-settings', plugin_dir_url(__FILE__) . 'css/admin.css');
    }

    /**
     * Check if any updates needs to be performed.
     */
    public function checkForUpgrade()
    {
        if (version_compare($this->settings->get('version'), self::VERSION, '<')) {
            $defaults = array(
                'php_settings' => array(),
                'update_config' => false,
                'restore_config' => true,
                'trim_comments' => true,
                'trim_whitespaces' => true,
                'notes' => array(
                    'review' => array(
                        'id' => 1,
                        'weight' => 1,
                        'persistent' => false,
                        'time' => 0,
                        'type' => 'info',
                        'name' => 'review',
                        'callback' => 'addReviewNotice',
                        'dismissed' => false,
                        'dismissible' => true,
                    ),
                    'support' => array(
                        'id' => 2,
                        'weight' => 2,
                        'persistent' => true,
                        'time' => 0,
                        'type' => 'warning',
                        'name' => 'support',
                        'callback' => 'addSupportNotice',
                        'dismissed' => true,
                        'dismissible' => false,
                    )
                )
            );
            $notes = $this->settings->get('notes');
            // Special handling for persistent notes.
            foreach ($defaults['notes'] as $id => $note) {
                if ($note['persistent'] && isset($notes[$id])) {
                    $defaults['notes'][$id]['dismissed'] = $notes[$id]['dismissed'];
                }
            }
            $this->settings->set('notes', $defaults['notes']);
            // Set defaults.
            foreach ($defaults as $key => $value) {
                $this->settings->add($key, $value);
            }
            $this->settings->set('version', self::VERSION);
            $this->settings->save();
        }
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
        self::removeSettings();
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
        $settings = new Settings(self::SETTINGS_NAME);
        if ($settings->get('restore_config')) {
            $configFile = self::getConfigFilePath();
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
        if ($screen->id === 'tools_page_custom-php-settings') {
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
        foreach ($this->settings->php_settings as $key => $value) {
            if (empty($value)) {
                if (!$this->settings->get('trim_whitespaces')) {
                    $section[] = '';
                }
            } elseif ($value[0] === '#') {
                if (!$this->settings->get('trim_comments')) {
                    $section[] = $value;
                }
            } else {
                $setting = explode('=', trim($value));
                $section[] = $cgiMode ? $setting[0] . '=' . $setting[1] : 'php_value ' . $setting[0] . ' ' . $setting[1];
            }
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
        if (self::createIfNotExist($configFile) === false) {
            /* translators: %s: Name of configuration file */
            $this->addSettingsMessage(sprintf(__('%s does not exists or is not writable.', self::TEXT_DOMAIN), $configFile));
            return;
        }
        $section = $this->getSettingsAsArray();
        /* translators: %s: Name of configuration file */
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
        if (count($setting) === 1) {
            if (strlen($setting[0]) === 0) {
                // This is a blank line.
                return 1;
            } elseif ($setting[0][0] === '#') {
                // This is a comment.
                return 1;
            } elseif (in_array($setting[0], $iniSettings)) {
                /* translators: %s: Name of PHP setting */
                $this->addSettingsMessage(sprintf(__('%s must be in the format: key=value', self::TEXT_DOMAIN), $setting[0]) . '<br />');
                return -2;
            }
        } elseif (count($setting) === 2 && in_array($setting[0], $iniSettings)) {
            return 1;
        }
        /* translators: %s: Name of PHP setting */
        $this->addSettingsMessage(sprintf(__('%s is not a valid setting.', self::TEXT_DOMAIN), $setting[0]) . '<br />');
        return -1;
    }

    /**
     * Handle form data for configuration page.
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
                // Filter and sanitize form values.
                $settings = array();
                $raw_settings = filter_input(
                    INPUT_POST,
                    'php_settings',
                    FILTER_SANITIZE_STRING
                );
                $raw_settings = array_map('trim', explode(PHP_EOL, trim($raw_settings)));
                foreach ($raw_settings as $key => $value) {
                    if ($this->validSetting($value) > 0) {
                        $settings[$key] = str_replace(';', '', $value);
                    }
                }
                $this->settings->set('php_settings', $settings);
                $this->settings->set('update_config', filter_input(
                    INPUT_POST,
                    'update_config',
                    FILTER_VALIDATE_BOOLEAN
                ));
                $this->settings->set('restore_config', filter_input(
                    INPUT_POST,
                    'restore_config',
                    FILTER_VALIDATE_BOOLEAN
                ));
                $this->settings->set('trim_comments', filter_input(
                    INPUT_POST,
                    'trim_comments',
                    FILTER_VALIDATE_BOOLEAN
                ));
                $this->settings->set('trim_whitespaces', filter_input(
                    INPUT_POST,
                    'trim_whitespaces',
                    FILTER_VALIDATE_BOOLEAN
                ));
                $this->settings->save();
                $this->settings->save();

                if ($this->settings->get('update_config')) {
                    $this->updateConfigFile();
                }
                // Check if we should activate the support notification.
                if (($notice = $this->getNoticeByName('support')) && $notice['time'] === 0) {
                    $this->resetNotice($notice['id']);
                }

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
        if ($this->getCurrentTab() === 'info' && $this->getCurrentSection()) {
            $template = __DIR__ . '/views/cps-' . $this->currentSection . '.php';
        } else {
            $template = __DIR__ . '/views/cps-' . $this->currentTab . '.php';
        }
        if (file_exists($template)) {
            require_once $template;
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
