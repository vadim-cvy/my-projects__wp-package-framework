<?php

namespace YOUR_NAMESPACE\framework\inc\site_pages;

use \YOUR_NAMESPACE\framework\inc\posts\aPost;
use \YOUR_NAMESPACE\framework\inc\posts\Post;

use \YOUR_NAMESPACE\framework\inc\wp\WP;
use \YOUR_NAMESPACE\framework\inc\wp\Hooks;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aPost_Page extends aSite_Page
{
    static public function get_post_object() : aPost
    {
        return new Post( static::get_post_id() );
    }

    abstract static public function get_post_id() : int;

    static public function get_raw_url() : string
    {
        return static::get_post_object()->get_url();
    }

    static public function is_current() : bool
    {
        return is_singular() && get_the_ID() === static::get_post_id();
    }

    static public function hooks() : Hooks
    {
        return static::hooks_base()
            ->create_child( 'post' )
            ->create_child( static::get_post_id() );
    }
}