<?php

namespace YOUR_NAMESPACE\framework\inc\package;

use \YOUR_NAMESPACE\framework\inc\design_patterns\tSingleton;

use \YOUR_NAMESPACE\framework\inc\cookies\Cookies;
use \YOUR_NAMESPACE\framework\inc\cookies\iCookies;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

class Package_Cookies implements iCookies
{
    use tSingleton;

    public function update( string $key, $value, int $expires = 0, string $path = '/'  ) : void
    {
        Cookies::update( Main::prefix( $key ), $value, $expires, $path );
    }

    public function delete( string $key, string $path = '/' ) : void
    {
        Cookies::delete( Main::prefix( $key ), $path );
    }

    public function get( string $key, $default_value = null )
    {
        return Cookies::get( Main::prefix( $key ), $default_value );
    }
}