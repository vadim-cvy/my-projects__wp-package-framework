<?php

namespace YOUR_NAMESPACE\framework\inc\taxonomies;

use \WP_Term;

use \YOUR_NAMESPACE\framework\inc\posts\iPostable;

if ( ! defined( 'ABSPATH' ) ) exit;

interface iTerm extends iPostable
{
    public function get_label() : string;

    public function get_description() : string;

    public function get_original() : WP_Term;
}