<?php

namespace YOUR_NAMESPACE\framework\inc\wp;

if ( ! defined( 'ABSPATH' ) ) exit;

class WP
{
    static public function hooks() : Hooks
    {
        return new Hooks();
    }

    static public function ajax() : AJAX
    {
        return new AJAX();
    }
}