<?php
namespace CustomPhpSettings\Common;

abstract class Singleton
{
    /**
     * @var array $instance
     */
    protected static $instance = array();

    /**
     * @return \CisionBlock\Common\Singleton|null
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
    abstract protected function __construct();
}
