<?php

namespace YOUR_NAMESPACE\framework\inc\user_input;

if ( ! defined( 'ABSPATH' ) ) exit;

trait tUser_Input_Textual
{
    public function validate_is_value_in( array $allowed_values ) : aUser_Input
    {
        if ( ! in_array( $this->get_normalized(), $allowed_values ) )
        {
            $this->throw_unexpected_value_error( $allowed_values );
        }

        return $this;
    }
}