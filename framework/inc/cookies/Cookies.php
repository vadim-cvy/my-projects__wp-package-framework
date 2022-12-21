<?php

namespace YOUR_NAMESPACE\framework\inc\cookies;

use \YOUR_NAMESPACE\framework\inc\data_types\Strings;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

class Cookies implements iCookies
{
    static public function update( string $key, $value, int $expires = 0, string $path = '/'  ) : void
    {
        if ( $expires === 0 )
        {
            $year_seconds = 60 * 60 * 24 * 365;

            $expires = time() + ( $year_seconds * 5 );
        }

        setcookie( $key, $value, $expires, $path );
    }

    static public function delete( string $key, string $path = '/' ) : void
    {
        static::update( $key, '', 1, $path );
    }

    static public function get( string $key, $default_value = null )
    {
        return ! empty( $_COOKIE[ $key ] ) ?
            static::normalize_cookie_value( $_COOKIE[ $key ] ) :
            $default_value;
    }

    static protected function normalize_cookie_value( string $key, $value )
    {
        if ( is_string( $value ) )
        {
            $value = Strings::cast( $value );
        }

        return $value;
    }
}