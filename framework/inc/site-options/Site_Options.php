<?php

namespace YOUR_NAMESPACE\framework\inc\site_options;

use \YOUR_NAMESPACE\framework\inc\data_types\Strings;

if ( ! defined( 'ABSPATH' ) ) exit;

class Site_Options
{
    static public function update( string $key, $value, $autoload = null ) : void
    {
        update_option( $key, $value, $autoload );
    }

    static public function delete( string $key ) : void
    {
        delete_option( $key );
    }

    static public function get( string $key, $default_value = null )
    {
        $value = isset( $default_value ) ? get_option( $key, $default_value ) : get_option( $key );

        return static::cast_value( $key, $value );
    }

    static protected function cast_value( string $key, $value )
    {
        if ( is_string( $value ) )
        {
            $value = Strings::cast( $value );
        }

        return $value;
    }
}