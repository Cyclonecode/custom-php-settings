<?php
namespace CustomPhpSettings\Config;

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
    * @var array
    */
    public $settings = array();

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
        return json_encode($this->toOptionsArray(), JSON_PRETTY_PRINT);
    }

    /**
     *
     */
    public function toYaml()
    {
        return yaml_emit($this->settings);
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
    *
    * @param mixed $value
    *   The value to set.
    */
    public function set($name, $value)
    {
        $this->settings[$name] = $value;
    }

    /**
    * Add a setting.
    *
    * @param string $name
    *   Name of setting to add.
    *
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
    *
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
        return update_option($this->optionName, $this->settings);
    }
}
