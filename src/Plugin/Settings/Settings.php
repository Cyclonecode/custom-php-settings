<?php

namespace CustomPhpSettings\Plugin\Settings;

class Settings implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * Name of configuration option.
     *
     * @var string
     */
    protected $optionName = '';

    /**
     * An array of settings.
     *
     * @var array $settings
     */
    protected $settings = array();

    /**
     * Version
     *
     * @var string
     */
    public $version = '1.0.6';

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
     * @return $this
     */
    public function delete()
    {
        \delete_option($this->optionName);
        $this->settings = array();
        return $this;
    }

    /**
     * Sets a configuration value.
     *
     * @param string $name
     *   Name of option to set.
     *
     * @param mixed $value
     *   The value to set.
     * @return $this
     */
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * Sets a configuration value.
     *
     * @param string $name
     *   Name of option to set.
     * @param mixed $value
     *   The value to set.
     * @return $this;
     */
    public function set($name, $value)
    {
        $this->settings[$name] = $value;
        return $this;
    }

    /**
     * Sets configuration from array.
     *
     * @param array $settings
     * @return $this
     */
    public function setFromArray(array $settings)
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }

    /**
     * Check if a setting isset.
     *
     * @param $key
     * @return bool
     */
    public function hasKey($key)
    {
        return $this->__isset($key);
    }

    /**
     * Check if a setting isset.
     *
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return $this->__isset($key);
    }

    /**
     * Check if a setting isset.
     *
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->settings[$key]);
    }

    /**
     * Add a setting.
     *
     * @param string $name
     *   Name of setting to add.
     * @param mixed $value
     *   Value to add.
     * @return $this
     */
    public function add($name, $value)
    {
        if (!isset($this->settings[$name])) {
            $this->set($name, $value);
        }
        return $this;
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
     * @param mixed $default
     *   Default value to return if settings does not exists.
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return (isset($this->settings[$name]) ? $this->settings[$name] : $default);
    }

    /**
     * Get a configuration value from array.
     *
     * @param string $name
     * @param int $index
     * @return mixed
     */
    public function getFromArray($name, $index)
    {
        $value = $this->get($name);
        if (is_array($value) && count($value) >= $index) {
            return $value[$index];
        }
    }

    /**
     * Remove setting.
     *
     * @param string $name
     *   Name of setting to remove.
     */
    public function __unset($name)
    {
        $this->remove($name);
    }

    /**
     * Remove setting.
     *
     * @param string $name
     *   Name of setting to remove.
     * @return $this
     */
    public function remove($name)
    {
        unset($this->settings[$name]);
        return $this;
    }

    /**
     * Rename setting.
     *
     * @param string $from
     *   Name of setting.
     * @param string $to
     *   New name for setting.
     * @return $this
     */
    public function rename($from, $to)
    {
        if (isset($this->settings[$from])) {
            $this->settings[$to] = $this->settings[$from];
            $this->remove($from);
        }
        return $this;
    }

    /**
     * Load settings from database.
     * @return $this
     */
    public function load()
    {
        $this->settings = \get_option($this->optionName);
        return $this;
    }

    /**
     * Save setting to database.
     *
     * @return bool
     */
    public function save()
    {
        ksort($this->settings);
        return \update_option($this->optionName, $this->settings);
    }

    /**
     * Save settings to file.
     *
     * @param $filename
     * @param string $format
     * @return false|int
     */
    public function saveToFile($filename, $format = 'json')
    {
        $content = '';
        switch ($format) {
            case 'raw':
                $content = serialize($this->settings);
                break;
            case 'json':
                $content = $this->toJSON();
                break;
            case 'yaml':
                $content = $this->toYaml();
                break;
        }
        return @file_put_contents($filename, $content);
    }

    /**
     * Removes any settings that is not defined in $options.
     *
     * @param array $options
     *   An array which keys will be used to validate the current settings keys.
     * @return $this
     */
    public function clean(array $options)
    {
        foreach ($this->settings as $key => $value) {
            if (!in_array($key, $options)) {
                unset($this->settings[$key]);
            }
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->settings);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->settings);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}
