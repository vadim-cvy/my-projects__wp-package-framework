<?php

namespace YOUR_NAMESPACE\framework\inc\posts;

if ( ! defined( 'ABSPATH' ) ) exit;

class Pages extends aPost_Type
{
    static public function get_slug() : string
    {
        return 'page';
    }

    static public function wrap_post( int $page_id ) : aPost
    {
        return new Page( $page_id );
    }
}