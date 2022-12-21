<?php

namespace YOUR_NAMESPACE\framework\inc\posts;

if ( ! defined( 'ABSPATH' ) ) exit;

class Page extends aPost
{
    protected function get_expected_post_type() : string
    {
        return Pages::get_slug();
    }
}