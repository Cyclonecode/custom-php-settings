<?php

namespace Cyclonecode\Plugin;

class Settings
{

    /**
     * Name of configuration option.
     *
     * @var string
     */
    private $optionName = '';

    /**
     * An array of settings.
     *
     * @var array $settings
     */
    private $settings = array();

    /**
     * Version
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Settings constructor.
     *
     * @param string $optionName
     */
    public function __construct($optionName)
    {
        $this->optionName = $optionName;
        $this->load();
    }

    /**
     * @return array
     */
    public function toOptionsArray()
    {
        return $this->settings;
    }

    /**
     * @return false|mixed|string|void
     */
    public function toJSON()
    {
        return function_exists('json_encode') ? json_encode($this->toOptionsArray(), JSON_PRETTY_PRINT) : '';
    }

    /**
     *
     */
    public function toYaml()
    {
        return function_exists('yaml_emit') ? yaml_emit($this->settings) : '';
    }

    /**
     * Returns the name of this option.
     *
     * @return string
     */
    public function getOptionName()
    {
        return $this->optionName;
    }

    /**
     * Delete this setting from database.
     */
    public function delete()
    {
        delete_option($this->optionName);
        $this->settings = array();
    }

    /**
     * Sets a configuration value.
     *
     * @param string $name
     *   Name of option to set.
     *
     * @param mixed $value
     *   The value to set.
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Sets a configuration value.
     *
     * @param string $name
     *   Name of option to set.
     * @param mixed $value
     *   The value to set.
     * @param string $section
     *   Name of section to use.
     */
    public function set($name, $value, $section = 'options')
    {
        $this->settings[$section][$name] = $value;
    }

    /**
     * Sets configuration from array.
     *
     * @param array $settings
     * @param string $section
     */
    public function setFromArray(array $settings, $section = 'options')
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value, $section);
        }
    }

    /**
     * Add a setting.
     *
     * @param string $name
     *   Name of setting to add.
     * @param mixed $value
     *   Value to add.
     * @param string $section
     *   Name of section to use.
     */
    public function add($name, $value, $section = 'options')
    {
        if (!isset($this->settings[$section][$name])) {
            $this->set($name, $value, $section);
        }
    }

    /**
     * Get a configuration value.
     *
     * @param string $name
     *   Name of option to get.
     *
     * @return mixed
     */
    public function __get($name)
    {
        // todo: we are marking this as protected for now
        return $this->get($name);
    }

    /**
     * Get a configuration value.
     *
     * @param string $name
     *   Name of option to get.
     * @param string $section
     *   Name of section to use.
     *
     * @return mixed
     */
    public function get($name, $section = 'options')
    {
        return (isset($this->settings[$section][$name]) ? $this->settings[$section][$name] : null);
    }

    /**
     * Remove setting.
     *
     * @param string $name
     *   Name of setting to remove.
     * @param string $section
     *   Name of section to use.
     */
    public function remove($name, $section = 'options')
    {
        unset($this->settings[$section][$name]);
    }

    /**
     * Rename setting.
     *
     * @param string $from
     *   Name of setting.
     * @param string $to
     *   New name for setting.
     * @param string $section
     *   Name of section.
     */
    public function rename($from, $to, $section = 'options')
    {
        if (isset($this->settings[$section][$from])) {
            $this->settings[$section][$to] = $this->settings[$section][$from];
            $this->remove($from, $section);
        }
    }

    /**
     * Load settings from database.
     */
    public function load()
    {
        $this->settings = get_option($this->optionName);
    }

    /**
     * Save setting to database.
     *
     * @return bool
     */
    public function save()
    {
        // ksort($this->settings);
        foreach ($this->settings as $section => $options) {
            ksort($this->settings[$section]);
        }
        return update_option($this->optionName, $this->settings);
    }

    /**
     * Removes any settings that is not defined in $options.
     *
     * @param array $options
     *   An array which keys will be used to validate the current settings keys.
     * @param string $section
     *   Name of section to use.
     */
    public function clean(array $options, $section = 'options')
    {
        if (is_array($options)) {
            $keys = array_keys($options);
            foreach ($this->settings[$section] as $key => $value) {
                if (!in_array($key, $keys)) {
                    unset($this->settings[$section][$key]);
                }
            }
        }
    }
}
