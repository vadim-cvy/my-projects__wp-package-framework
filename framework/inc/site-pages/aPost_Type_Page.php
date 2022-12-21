<?php

namespace YOUR_NAMESPACE\framework\inc\site_pages;

use \YOUR_NAMESPACE\framework\inc\templates\aView_Controller;

use \YOUR_NAMESPACE\framework\inc\posts\aPost_Type;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aPost_Type_Page extends aSite_Page
{
    static protected function get_raw_url() : string
    {
        return static::get_post_type_object()->get_archive_url();
    }

    abstract static protected function get_post_type_object() : aPost_Type;

    static public function is_current() : bool
    {
        $post_type = static::get_post_type_object();

        return
            is_admin() ?
            $post_type->is_screen_match( 'edit' ) :
            $post_type->is_archive_page();
    }
}