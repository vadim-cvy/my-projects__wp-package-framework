<?php

namespace YOUR_NAMESPACE\framework\inc\posts;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Implements common methods to make it easier to work with post types.
 */
class Post_Types
{
    public static function get_visible() : array
    {
        return get_post_types([
            'public'  => true,
            'show_ui' => true,
        ], 'objects' );
    }
}
