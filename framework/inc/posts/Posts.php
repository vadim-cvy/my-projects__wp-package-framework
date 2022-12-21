<?php

namespace YOUR_NAMESPACE\framework\inc\posts;

if ( ! defined( 'ABSPATH' ) ) exit;

class Posts extends aPost_Type
{
    static public function get_slug() : string
    {
        return 'post';
    }

    static public function is_archive_page() : bool
    {
        return is_home();
    }
}