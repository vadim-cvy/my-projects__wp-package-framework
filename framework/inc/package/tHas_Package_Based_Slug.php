<?php

namespace YOUR_NAMESPACE\framework\inc\package;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

trait tHas_Package_Based_Slug
{
    static public function get_slug() : string
    {
        return Main::prefix( static::get_slug_base() );
    }

    abstract static protected function get_slug_base() : string;
}