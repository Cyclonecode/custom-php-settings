<?php

namespace Cyclonecode\Plugin;

abstract class Singleton
{
    /**
     * @var array $instance
     */
    protected static $instance = array();

    /**
     * @return \CustomPhpSettings\Common\Singleton|null
     */
    final public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$instance[$class]) || !self::$instance[$class] instanceof $class) {
            self::$instance[$class] = new static();
        }
        return static::$instance[$class];
    }

    /**
     * Singleton constructor.
     */
    final private function __construct()
    {
        $this->init();
    }

    /**
     * Prevent instantiation.
     */
    final private function __clone()
    {
    }

    protected function init()
    {
    }
}
