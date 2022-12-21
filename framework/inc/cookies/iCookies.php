<?php

namespace YOUR_NAMESPACE\framework\inc\cookies;

if ( ! defined( 'ABSPATH' ) ) exit;

interface iCookies
{
    public function update( string $key, $value, int $expires = 0, string $path = '/'  ) : void;

    public function delete( string $key, string $path = '/' ) : void;

    public function get( string $key, $default_value = null );
}