<?php

namespace YOUR_NAMESPACE\framework\inc\design_patterns;

if ( ! defined( 'ABSPATH' ) ) exit;

trait tSingleton
{
    protected static $instances = [];

    public static function get_instance() : object
    {
        $class = get_called_class();

        if ( ! isset( static::$instances[ $class ] ) )
        {
            static::$instances[ $class ] = new static();
        }

        return static::$instances[ $class ];
    }

    protected function __construct() {}
}