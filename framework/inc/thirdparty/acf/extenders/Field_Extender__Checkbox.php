<?php

namespace YOUR_NAMESPACE\framework\inc\thirdparty\acf\extenders;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

if ( ! defined( 'ABSPATH' ) ) exit;

class Field_Extender__Checkbox
{
    public static function prevent_empty_other_option() : void
    {
        WP::hooks()->listen( 'acf/validate_value/type=checkbox',
            [ get_called_class(), '_maybe_validate_empty_value' ] );
    }

    public static function _maybe_validate_empty_value( $valid, $value )
    {
        if ( count( $value ) !== count( array_filter( $value ) ) )
        {
            $valid = 'Some of submitted options are empty!';
        }

        return $valid;
    }
}