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
     * @return false|string
     */
    public function toJSON()
    {
        return function_exists('json_encode') ? json_encode($this->toOptionsArray(), JSON_PRETTY_PRINT) : '';
    }

    /**
     * @return string
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
     * Returns the current version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
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
     */
    public function set($name, $value)
    {
        $this->settings[$name] = $value;
    }

    /**
     * Sets configuration from array.
     *
     * @param array $settings
     */
    public function setFromArray(array $settings)
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Add a setting.
     *
     * @param string $name
     *   Name of setting to add.
     * @param mixed $value
     *   Value to add.
     */
    public function add($name, $value)
    {
        if (!isset($this->settings[$name])) {
            $this->set($name, $value);
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
        return $this->get($name);
    }

    /**
     * Get a configuration value.
     *
     * @param string $name
     *   Name of option to get.
     *
     * @return mixed
     */
    public function get($name)
    {
        return (isset($this->settings[$name]) ? $this->settings[$name] : null);
    }

    /**
     * Remove setting.
     *
     * @param string $name
     *   Name of setting to remove.
     */
    public function remove($name)
    {
        unset($this->settings[$name]);
    }

    /**
     * Rename setting.
     *
     * @param string $from
     *   Name of setting.
     * @param string $to
     *   New name for setting.
     */
    public function rename($from, $to)
    {
        if (isset($this->settings[$from])) {
            $this->settings[$to] = $this->settings[$from];
            $this->remove($from);
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
        ksort($this->settings);
        return update_option($this->optionName, $this->settings);
    }

    /**
     * Removes any settings that is not defined in $options.
     *
     * @param array $options
     *   An array which keys will be used to validate the current settings keys.
     */
    public function clean(array $options)
    {
        if (is_array($options)) {
            foreach ($options as $key => $value) {
                if (!in_array($key, $this->settings)) {
                    unset($this->settings[$key]);
                }
            }
        }
    }
}
