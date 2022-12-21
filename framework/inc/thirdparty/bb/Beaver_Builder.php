<?php

namespace YOUR_NAMESPACE\framework\inc\thirdparty\bb;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Helps to work with Beaver Builder plugin data.
 */
class Beaver_Builder
{
    static public function is_editor_screen() : bool
    {
        // Todo: maybe there is another more clear way.
        // This one may be buggy, ie ajax requests may be treated as editor screen.
        return isset( $_GET['fl_builder'] );
    }

    static public function is_ajax_action( string $action_name ) : bool
    {
        return ! empty( $_POST['fl_builder_data'] ) &&
            ! empty( $_POST['fl_builder_data']['action'] ) &&
            $_POST['fl_builder_data']['action'] === $action_name;
    }
}