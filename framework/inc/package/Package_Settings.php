<?php

namespace YOUR_NAMESPACE\framework\inc\package;

use \YOUR_NAMESPACE\framework\inc\site_options\Site_Options;

use \YOUR_NAMESPACE\Main;

use \Exception;

if ( ! defined( 'ABSPATH' ) ) exit;

class Package_Settings extends Site_Options
{
    static public function update( string $key, $value, $autoload = null ) : void
    {
        parent::update( Main::prefix( $key ), $value, $autoload );
    }

    static public function delete( string $key ) : void
    {
        parent::delete( Main::prefix( $key ) );
    }

    static public function get( string $key, $default_value = null )
    {
        $key = Main::prefix( $key );

        if ( ! static::has( $key ) )
        {
            throw new Exception( 'Setting "' . $key . '" is not registered.' );
        }

        return parent::get( $key, $default_value );
    }

    static public function has( string $key )
    {
        $key = Main::prefix( $key );

        return ! empty( get_registered_settings()[ $key ] );
    }
}