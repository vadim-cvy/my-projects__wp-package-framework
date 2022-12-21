<?php

namespace YOUR_NAMESPACE\framework\inc\thirdparty\acf\extenders;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

class Field_Extender__True_False
{
    public static function maybe_add_ui_class() : void
    {
        WP::hooks()->listen( 'acf/field_wrapper_attributes', [ get_called_class(), '_maybe_add_ui_class' ] );
    }

    public static function _maybe_add_ui_class( array $filed_wrapper_attrs, array $field ) : array
    {
        if ( $field['type'] !== 'true_false' || empty( $field['ui'] ) )
        {
            return $filed_wrapper_attrs;
        }

        $ui_class = Main::get_slug() . '-is-ui';

        if ( strpos( $filed_wrapper_attrs['class'], $ui_class ) === false )
        {
            $filed_wrapper_attrs['class'] .= ' ' . $ui_class;
        }

        return $filed_wrapper_attrs;
    }
}