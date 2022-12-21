<?php

namespace YOUR_NAMESPACE\framework\inc\posts;

if ( ! defined( 'ABSPATH' ) ) exit;

class Post extends aPost
{
    protected function get_expected_post_type() : string
    {
        return Posts::get_slug();
    }
}