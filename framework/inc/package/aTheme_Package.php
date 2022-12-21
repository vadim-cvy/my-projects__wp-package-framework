<?php

namespace YOUR_NAMESPACE\framework\inc\package;

use \YOUR_NAMESPACE\framework\inc\pluggable\Pluggable;
use \YOUR_NAMESPACE\framework\inc\pluggable\Theme;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aTheme_Package extends aPackage
{
    // todo: make this to be calculated in aPackage automatically
    static public function get_type() : string
    {
        return 'theme';
    }

    static public function get_wrapped_pluggable() : Pluggable
    {
        return new Theme( Main::fs()->get_main_file() );
    }
}