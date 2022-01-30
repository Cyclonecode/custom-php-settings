<?php

namespace CustomPhpSettings\Plugin\Common;

abstract class Singleton
{
    /**
     * @var array $instance
     */
    protected static $instance = array();

    /**
     * @return mixed
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
    private function __clone()
    {
    }

    /**
     * @throws \Exception
     */
    final public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton');
    }

    protected function init()
    {
    }
}
